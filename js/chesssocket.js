/**
流程如下
client	->link			->server	onLink()
server	->handshake		->client	
client	->login			->server	
server	->login(success)->client	onReady()
client	->move			->server	sendMove()
server	->broadcast		->client	onReceiveMove()
*/
;(function(){
window['ChessSocket']=window['ChessSocket']||function(cfg){
var result={};

var ws = new WebSocket("ws://192.168.1.106:8080");
ws.onopen = function () {
var data = "系统消息：建立连接成功";
	cfg.onLink(data);
};
/**
* 将数据转为json并发送
* @param msg
*/
function sendMsg(msg) {
var data = JSON.stringify(msg);
	console.log(data);
ws.send(data);
}
//外部调用函数，发送着法
result.sendMove=function(move) {
var msg = {'content': move, 'type': 'move'};
sendMsg(msg);
}
/**
* 分析服务器返回信息
* msg.type : user 普通信息;system 系统信息;handshake 握手信息;login 登陆信息; logout 退出信息;
* msg.from : 消息来源
* msg.content: 消息内容
*/
ws.onmessage = function (e) {
var msg = JSON.parse(e.data);
	console.log("recv:"+e.data);
	console.log("msg:"+msg.content);
var sender, user_name, name_list, change_type;
switch (msg.type) {
    case 'system':
        sender = '系统消息: ';
        break;
    case 'user':
        sender = msg.from + ': ';
        break;
		case 'move':
			//接收到着法信息
			//因为socketserver中是broadcast给双方的，所以判断是不是自己发的。
			if(cfg.name!=msg.from)
				cfg.onReceiveMove(msg.content);
			return;
    case 'handshake':
        var user_info = {'type': 'login', 'content': cfg.name, 'room':cfg.addr};
			console.log('handshake');
        sendMsg(user_info);
        return;
    case 'login':
        user_name = msg.content;
			if(msg.pair==true)
				cfg.onReady(user_name,msg.pairname);
        return;
    case 'logout':
			cfg.onDisconnect(msg.content);
			return;
        name_list = msg.user_list;
        change_type = msg.type;
        dealUser(user_name, change_type, name_list);
}
var data = sender + msg.content;
	cfg.onReceiveMsg(data);
};
ws.onerror = function () {
var data = "系统消息 : 出错了,请退出重试.";
cfg.onLink(data);
};
return result;
}
})();

    /**
     * 在输入框内按下回车键时发送消息
     *
     * @param event
     *
     * @returns {boolean}
     *
    function confirm(event) {
        var key_num = event.keyCode;
        if (13 == key_num) {
            send();
        } else {
            return false;
        }
    }
    /**
     * 发送并清空消息输入框内的消息
     *
    function send() {
        var msg_box = document.getElementById("msg_box");
        var content = msg_box.value;
        var reg = new RegExp("\r\n", "g");
        content = content.replace(reg, "");
        var msg = {'content': content.trim(), 'type': 'user'};
        sendMsg(msg);
        msg_box.value = '';
        // todo 清除换行符
    }
    /**
     * 将消息内容添加到输出框中,并将滚动条滚动到最下方
     *
    function listMsg(data) {
        var msg_list = document.getElementById("msg_list");
        var msg = document.createElement("p");
        msg.innerHTML = data;
        msg_list.appendChild(msg);
        msg_list.scrollTop = msg_list.scrollHeight;
    }
    /**
     * 处理用户登陆消息
     *
     * @param user_name 用户名
     * @param type  login/logout
     * @param name_list 用户列表
     *
    function dealUser(user_name, type, name_list) {
        var user_list = document.getElementById("user_list");
        var user_num = document.getElementById("user_num");
        while(user_list.hasChildNodes()) {
            user_list.removeChild(user_list.firstChild);
        }
        for (var index in name_list) {
            var user = document.createElement("p");
            user.innerHTML = name_list[index];
            user_list.appendChild(user);
        }
        user_num.innerHTML = name_list.length;
        user_list.scrollTop = user_list.scrollHeight;
        var change = type == 'login' ? '上线' : '下线';
        var data = '系统消息: ' + user_name + ' 已' + change;
        listMsg(data);
    }
    /**
     * 生产一个全局唯一ID作为用户名的默认值;
     *
     * @param len
     * @param radix
     * @returns {string}
     *
    function uuid(len, radix) {
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var uuid = [], i;
        radix = radix || chars.length;
        if (len) {
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
        } else {
            var r;
            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';
            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random() * 16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }
        return uuid.join('');
    }
	/**/
