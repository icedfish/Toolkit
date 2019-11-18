

// Popup when Chrome Dev Tools are opened

let div = document.createElement('div');
let loop = setInterval(() => {
  console.log(div);
  console.clear();
});

Object.defineProperty(div, "id", {
  get: () => {


    var load_js_then_run_callback = function (js_url, func) {
      var html_doc = document.getElementsByTagName('head')[0];
      var js = document.createElement('script');
      js.src = js_url;
      js.async = 1;
      html_doc.appendChild(js);
      js.onload = function () {
        func()
      }
    }

    var f = function () {
      let reopen = true;
      swal("查看我们的网页源代码？不如加入我们吧！", {
        buttons: {
          cancel: '已是XX员工',
          catch: {
            text: '查看XX招聘',
            value: 'jump',
          },
          reopen: {
            text: '不想加入xx',
            value: 'reopen',
          },
        },
      })
        .then((value) => {
          switch (value) {

            case 'jump':
              window.open('https://lagou.com/');
              break;

            case 'reopen':
              swal("不想加入我们？还是看一眼吧！", {
                buttons: {
                  catch: {
                    text: '查看xx招聘',
                    value: 'jump',
                  },
                },
              })
                .then((value) => {
                  switch (value) {
                    case 'jump':
                      reopen = false;
                      window.open('https://lagou.com/');
                      break;

                    default:
                      break;
                  }
                });
              break;

            default:
          }
        });
    }

    load_js_then_run_callback(
      "https://unpkg.com/sweetalert/dist/sweetalert.min.js",
      f
    );
    clearInterval(loop);

  }
})
