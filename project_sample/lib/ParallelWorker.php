<?php

/*
 *  方便简单的在本地实现多线程操作，免得依赖Queue+Worker的模式才能享受到并发的好处。
 *  所有$callable_function的实现需要是线程安全的。
 *  执行不一定是顺序的，可能是乱序的。
 *  现在的一个局限：
 *     执行结果没办法通过consume()函数直接返回，如果需要把worker的执行结果拿回来的话，自己用LocalMessageQueue传递吧！
 */

// need pcntl installed (add --enable-pcntl when config php)

class ParallelWorker {
	private $callable_function = null;
	private $limit = 1;
	private $workers = [];

	/**
	 * @param string $callable_function
	 * @param int $parallel_limit
	 */
	public function __construct($callable_function, $parallel_limit = 10) {
		if (!is_callable($callable_function)) {
			throw new Exception("You must give a callable function!");
		} else {
			$this->callable_function = $callable_function;
			$shutdown = function (ParallelWorker $o){
				foreach ($o->workers as $pid => $worker) {
					posix_kill($pid, 9);
				}
			};
			register_shutdown_function($shutdown, $this);
		}
		$this->limit = $parallel_limit;
	}

	public function consume($data) {
		if (count($this->workers) < $this->limit) {
			$this->add_worker($data);
		}

		//确保有空余的worker了才返回，保证下次调用consume()的时候简单.
		//@todo 增加任务超时判断，超时后需要重试功能。
		while (count($this->workers) >= $this->limit) {
			$pid = pcntl_wait($status);
			if ($pid !== false && isset($this->workers[$pid])) {
				unset($this->workers[$pid]);
			} else {
				usleep(5000);
			}
		}
	}

	private function log($str) {
		Logger::log($str, 'parallel_worker');
	}

	private function add_worker($data) {
		$pid = pcntl_fork(); //创建子进程
		if ($pid == 0) { //子进程进入了这个岔路口，父进程直接执行if后面的代码
			$this->workers = [];//清空，避免shutdown function生效
			call_user_func($this->callable_function, $data);
			$this->log("Finish data '" . json_encode($data) . "'");
			exit; //子进程必须退出，否则还会继续执行if后面的代码
		} else { //主进程
			$this->log("Start data '" . json_encode($data) . "' by {$pid}");
			$this->workers[$pid] = $data;
		}
	}

}

// sample code:
/*
function test($arr) {
	list($id, $val) = $arr;
	flush();
	usleep(rand(100000, 999999));
	echo "doing $id => $val\n"; //$count will not change ^_^
}
$p = new ParallelWorker('test', 3);
foreach ([[1, 'one'], [2,'two'], [3,'three'], [4, 'four'], [5,'five'], [6, 'six']] as $l) {
	$p->consume($l);
}
*/