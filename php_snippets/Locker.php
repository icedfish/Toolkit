<?php
// 单机锁

//flock() allows you to perform a simple reader/writer model which can be used on virtually every platform (including most Unix derivatives and even Windows).

/* Sample Code:

$l = new Locker('job-daily-send-coupon');
if ($l->lock()) {
    echo 'Lock Success';

    var_dump($l->lock()); //return true; can lock multi times;
    // code to send coupons

    sleep(30);
    $l->unlock();
} else {
    echo 'Locked by other process!';
    // skip job
}
*/

class Locker
{
    const TEMP_DIR = '/tmp/';

    private $key = null;
    private $handler = null;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function lock()
    {
        $this->handler = fopen($this->file(), 'w+');

        return flock($this->handler, LOCK_EX | LOCK_NB);    // 独占锁、非阻塞
    }

    public function unlock()
    {
        if ($this->handler) {
            fclose($this->handler);
            @unlink($this->file());
            $this->handler = null;
        }
    }

    public function __destruct()
    {
        $this->unlock();
    }

    private function file()
    {
        //需要避免传入的key可能包含特殊字符，作文件名有问题
        return self::TEMP_DIR . md5($this->key) . '.locker';
    }

}

