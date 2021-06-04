var chat, form;

function onLoad() {
    chat = document.getElementById("chat");
    form = document.getElementById("town");
    new_game(); //при загрузке страницы начинаем новую игру
    form.focus();
}

//запиываем в чат всё что ответает сервер
function write_to_chat(message) {
    chat.innerText += message;
    chat.scrollTop = chat.scrollHeight;
}

//функуия получения города и очистки формы
function getTownAndClear() {
    var res = form.value;
    form.value = "";
    return res;
}

//запрос на сервер и получение ответа
function processRequest(uri) {
    fetch(uri, {
        credentials: 'same-origin'
    }).then((response) => {
        return response.text();
    }).then(value => {
        write_to_chat(value); //вызываем получив ответ
    }).catch(reason => {
        console.error(reason);
    });
}

function new_game() {
    processRequest('./php/goroda.php?act=new_game');
    chat.innerText="";
}

//ф-ция следующего шага
function new_turn() {
    processRequest('./php/goroda.php?act=new_turn&town=' + encodeURI(getTownAndClear()));
}

//привязываем нажатие Enter для отправки формы
function processEnter(e) {
    if (e.keyCode === 13) {
        new_turn()
    }
}