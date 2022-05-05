let tag = document.currentScript.getAttribute('tg');

//Cookies
function getCookieSlsv(offset) {
    var endstr = document.cookie.indexOf(";", offset);
    if (endstr == -1)
        endstr = document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg){
            return getCookieSlsv(j);
        }
            
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0)
            break;
    }

    return null;
}

function verificaCookieSessao(){
    
    let referrer = document.referrer;
    let cookie = GetCookie('contador_solutions');
    
    if(cookie !== null){
        cookie = JSON.parse(cookie);

        console.log([cookie.referrer, referrer]);
        
        if(cookie.referrer != referrer){
            console.log('trocou Referrer');
            cookie = null;
        }else{
            return cookie;
        }
    }
    
    return cookie;
}

function dataExpiracao(){

    //Tempo de validade da sessão
    let tempo_expiracao = 0.5; //Meia hora 

    data = new Date;
    data.setHours(23, 40, 59);

    //Verifica se expiração vai passar da meia noite
    dia_hoje  = data.getDay();
    
    let verificar = data.getTime();
    verificar += (1000 * 60 * 30);

    data_verificar = new Date(verificar);
    dia_verificar = data_verificar.getDay();

    //Caso passe, o vencimento fica à meia noite
    if(dia_verificar != dia_hoje){
        data.setHours(23, 59, 59);

    //Caso contrário, vence na hora de agora + 30 minutos
    }else{
        data.setTime(data.getTime() + (tempo_expiracao * 60 * 60 * 1000));
    }

    return data;
}

function SetCookie(cname, cvalue) {

    //Data de expiração
    let data_expiracao = dataExpiracao();
    var expires = "expires=" + data_expiracao.toUTCString();

    //Criação do cookie
    let referrer = document.referrer;
    
    let valor = {
        k: cvalue,
        referrer: referrer
    }

    valor = JSON.stringify(valor);

    document.cookie = cname + "=" + valor + "; " + expires;

    console.log('Cookie criado');
}

function DeleteCookie(name) {
    var exp = new Date();
    FixCookieDate(exp);
    exp.setTime(exp.getTime() - 1);
    var cval = GetCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + "; expires=" + exp.toUTCString();
}

//Devices
function getTipoDispositivo() {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
        return "tablet";
    }
    else if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
        return "mobile";
    }
    return "desktop";
}

//Requests
function makeRequest(method, url) {

    return new Promise(function (resolve, reject) {

        let xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.onload = function () {

            if (this.status >= 200 && this.status < 300) {
                resolve(xhr.response);
            } else {
                reject({
                    status: this.status,
                    statusText: xhr.statusText
                });
            }
        };
        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        };

        xhr.send();
    });
}

function verificaUrl(){

    return true;//REMOVER ************

    let cookie = GetCookie('pgs');
    let url = document.location.pathname;

    if(cookie === null){
        let visita = { 'pgs': []};
        visita.pgs.push(url);

        visita = JSON.stringify(visita);
        
        //Cria cookie
        document.cookie = 'pgs' + "=" + visita + "; ";

        return true;
    }else{
        
        cookie = JSON.parse(cookie);

        if(cookie.pgs.indexOf(url) === -1){
            
            console.log(cookie.pgs.indexOf(url), cookie.pgs);
            cookie.pgs.push(url);
            cookie = JSON.stringify(cookie);
            document.cookie = 'pgs' + "=" + cookie + "; ";
            
            return true;
        }

        console.log('pagina já visitada');
        return false;
    }
}

function zeraUrl(){
    let visita = { 'pgs': []};
    visita = JSON.stringify(visita);
        
    //Cria cookie
    document.cookie = 'pgs' + "=" + visita + "; ";
}

//Função principal para registrar as visistas
async function registraVisita(k, tag) {
    
    if(!tag){
        return false;
    }

    let xhr = new XMLHttpRequest();
    let url = "http://localhost:8000/public/tracker/collect"; //Url do endpoind que registra as visitas (LOCAL)
    //let url = "https://leonardosouza.com.br/contadorSolutions.php"; //Url do endpoind que registra as visitas

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Access-Control-Allow-Origin", "*");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {

        }
    };

    let ip = await makeRequest("GET", "https://api.ipify.org/?format=json");
    ip = JSON.parse(ip);
    ip = ip.ip;

    // let localizacao = await makeRequest("GET", "http://ip-api.com/json/" + ip);
    // localizacao = JSON.parse(localizacao);

    let dispositivo = getTipoDispositivo();
    let url_atual = window.location.href;
    let referrer = document.referrer;

    //Resolução do dispositivo
    let altura = window.screen.height;
    let largura = window.screen.width;
    let resolucao = largura + 'x' + altura;

    //Sistema operacional
    var os = '';
    var nAgt = navigator.userAgent;
    var clientStrings = [
        {s:'Windows 10', r:/(Windows 10.0|Windows NT 10.0)/},
        {s:'Windows 8.1', r:/(Windows 8.1|Windows NT 6.3)/},
        {s:'Windows 8', r:/(Windows 8|Windows NT 6.2)/},
        {s:'Windows 7', r:/(Windows 7|Windows NT 6.1)/},
        {s:'Windows Vista', r:/Windows NT 6.0/},
        {s:'Windows Server 2003', r:/Windows NT 5.2/},
        {s:'Windows XP', r:/(Windows NT 5.1|Windows XP)/},
        {s:'Windows 2000', r:/(Windows NT 5.0|Windows 2000)/},
        {s:'Windows ME', r:/(Win 9x 4.90|Windows ME)/},
        {s:'Windows 98', r:/(Windows 98|Win98)/},
        {s:'Windows 95', r:/(Windows 95|Win95|Windows_95)/},
        {s:'Windows NT 4.0', r:/(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
        {s:'Windows CE', r:/Windows CE/},
        {s:'Windows 3.11', r:/Win16/},
        {s:'Android', r:/Android/},
        {s:'Open BSD', r:/OpenBSD/},
        {s:'Sun OS', r:/SunOS/},
        {s:'Chrome OS', r:/CrOS/},
        {s:'Linux', r:/(Linux|X11(?!.*CrOS))/},
        {s:'iOS', r:/(iPhone|iPad|iPod)/},
        {s:'Mac OS X', r:/Mac OS X/},
        {s:'Mac OS', r:/(Mac OS|MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
        {s:'QNX', r:/QNX/},
        {s:'UNIX', r:/UNIX/},
        {s:'BeOS', r:/BeOS/},
        {s:'OS/2', r:/OS\/2/},
        {s:'Search Bot', r:/(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
    ];

    for (var id in clientStrings) {
        var cs = clientStrings[id];
        if (cs.r.test(nAgt)) {
            os = cs.s;
            break;
        }
    }

    //let data = JSON.stringify({ "ip": ip, "localizacao": localizacao, dispositivo: dispositivo });

    visita = { 
               tag: tag,
               ip: ip, 
               url: url_atual, 
               dispositivo: dispositivo, 
               k: k, 
               referrer: referrer, 
               host: window.location.host,
               path: window.location.pathname,
               search: window.location.search,
               resolucao:resolucao, 
               os:os 
            }

    let data = JSON.stringify(visita);

    //console.log(visita);
    xhr.send(data);
}

//Valida Cookie de sessão
let cookie = verificaCookieSessao();

let k = '';

//Se o cookie não existir, ele vai ser iniciado
if (cookie === null) {

    //Chave pra sessão
    let p1 = Math.random().toString(36).substring(0, 10);
    let p2 = Math.random().toString(36).substring(0, 10);
    k = p1 + p2;

    SetCookie('contador_solutions', k);
    zeraUrl();

    //Se o cookie existir, ele vai ser registrado novamente e o tempo de sessão do usuário será reiniciado
} else {
    k = cookie.k;
}

//verifica se url já foi registrada, valida cookie de visita 
let registrar_visita = verificaUrl();
if(registrar_visita){
    registraVisita(k, tag);
}