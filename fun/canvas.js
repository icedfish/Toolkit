// 用Canvas特性识别设备指纹

function bin2hex(s) {
    // discuss at: http://phpjs.org/functions/bin2hex/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Onno Marsman
    // bugfixed by: Linuxworld
    // improved by: ntoniazzi (http://phpjs.org/functions/bin2hex:361#comment_177616)
    // example 1: bin2hex('Kev');
    // returns 1: '4b6576'
    // example 2: bin2hex(String.fromCharCode(0x00));
    // returns 2: '00'
    var i, l, o = '', n;
    s += '';
    for (i = 0, l = s.length; i < l; i++) {
        n = s.charCodeAt(i)
            .toString(16);
        o += n.length < 2 ? '0' + n : n;
    }
    return o;
}

var canvas = document.createElement('canvas');
var ctx = canvas.getContext('2d');
var txt = 'http://security.tencent.com/';
ctx.textBaseline = "top";
ctx.font = "14px 'Arial'";
ctx.textBaseline = "tencent";
ctx.fillStyle = "#f60";
ctx.fillRect(125, 1, 62, 20);
ctx.fillStyle = "#069";
ctx.fillText(txt, 2, 15);
ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
ctx.fillText(txt, 4, 17);

var b64 = canvas.toDataURL().replace("data:image/png;base64,", "");
var bin = atob(b64);
var crc = bin2hex(bin.slice(-16, -12));
console.log(crc);

console.log(bin2hex(bin));