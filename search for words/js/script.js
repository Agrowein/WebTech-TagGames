//Событию загрузки страницы указываем функцию-коллбэк
window.onload = function() {
    //получаем элемент кнопки по ID
    var btn = document.getElementById('request-btn');

    //Указываем обработчик кнопки
    btn.onclick = request_btn_handler;

    //Блокируем перевод строки в поле ввода искомого слова
    lock_contenteditable("word_field");
}


function request_btn_handler() {
    //Выбираем данные из полей ввода
    var text = document.getElementById('text_field').textContent;
    var word = document.getElementById('word_field').textContent;

    //Вызываем йункцию запроса
    request({text: text, word: word});

    
}

//Функция запроса. Формируем POST запрос 
//и через fetch() передаем в form_handler.php
function request(data) {
    if (data.text != '' && data.word != '')  {
        var response = fetch('./php/form_handler.php', {
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
              },
            method: 'POST',
            body: JSON.stringify({
                text: data.text,
                word: data.word
            })
        })
        .then(response => response.json())
        .then(response => put_result(response))
        .catch((error) => {
            console.log(error);
        });
    }
}

//Функция добавления результата в документ
function put_result(response) {
    var text = response.text ?? '';
    var count = response.count ?? 0;
    $('#text_field').html(text);
    document.getElementById('count').innerText = count;
}


//Функция блокировки перевода строки в поле ввода искомого слова
function lock_contenteditable(id) {
    const div = document.getElementById(id);

    div.addEventListener('keydown', function (e) {
        if (e.code === 'Enter') {
            e.preventDefault()
        }
    });

    div.addEventListener('input', function (e) {
        if (e.inputType === 'insertParagraph') {
            e.preventDefault()
        }
    });
}