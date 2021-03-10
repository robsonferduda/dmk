var https = require('https');
var fs = require('fs');

var options = {
    key: fs.readFileSync('/home/easyjuris/ssl/keys/e0bd5_92b8b_80d31300b3eed904ba45537afcc8eb43.key'),
    cert: fs.readFileSync('/home/easyjuris/ssl/certs/easyjuris_com_br_e0bd5_92b8b_1618539860_cde5b8a4797fe9afe1150d1459e76d63.crt'),
  passphrase: "duda"
};

var a = https.createServer(options, function (req, res) {
 
	res.setHeader('Content-Type', 'application/json');
	res.setHeader('X-Powered-By', 'coperve');
	res.writeHead(200);
  res.end("Servidor Node + Redis rodando...\n");
});

var io = require('socket.io')(a);

var Redis = require('ioredis');
var redis = new Redis();
redis.subscribe('notificacao', function(err, count) {
});
redis.on('message', function(channel, message) {
    console.log('Message Recieved: ' + message);
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data);
});

a.listen(3000);
