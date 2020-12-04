//глобальные переменные
var arr = [];
var box;
var score = 0;
var data;//переменная счета
//открытие окна победы
function example() {
	el = document.getElementById("example");
	if (el.style.visibility == "visible") {
		el.style.visibility = "hidden";
	} else {
		el.style.visibility = "visible";
	}
	num = document.getElementById("score-victory");
	num.innerHTML = score;
}
//загрузка страницы
window.onload = async function() {
	box = document.getElementById("box");
    var data = await postData({username: "", userscore: "0"});
    setTable(data, document.querySelector('.top-table'));
	newGame();
	document.getElementById("reset").onclick = newGame;
}
//функция перемещивания
function swap(arr,i1,j1,i2,j2) {
	t = arr[i1][j1];
	arr[i1][j1] = arr[i2][j2];
	arr[i2][j2] = t;
}
//новая игра
function newGame() {
	//зануляем счет
	score = 0;
	document.getElementById("score").innerHTML = score;
	//создаем массив2 чисел
	for (i = 0; i < 4; i++) {
		arr[i] = [];
		for (j = 0; j < 4; ++j) {
			if (i+j != 6) {
				arr[i][j] = i*4 + j + 1;
			}
			else {
				arr[i][j] = "";
			}
		}
	}
	//перемешиваем массив
	ei = 3;
	ej = 3;
	for(i = 0; i < 10000; ++i) {
		switch(Math.round(3*Math.random())) {
			case 0:
				if(ei > 0) swap(arr,ei,ej,--ei,ej);
				break;
			case 1:
				if(ej < 3) swap(arr,ei,ej,ei,++ej);
				break;
			case 2:
				if(ei < 3) swap(arr,ei,ej,++ei,ej);
				break;
			case 3: if(ej > 0) swap(arr,ei,ej,ei,--ej);
			break;
			default:
				alert("Таких значений нет");
		}
	}
	//обновляем поля
	var table = document.getElementById("table");
	var trs = table.getElementsByTagName("tr");
	var tds = null;
	for (i = 0; i<trs.length; i++) {
		tds = trs[i].getElementsByTagName("td");
		for (j = 0; j<trs.length; j++) {
			tds[j].onclick = cellClick;
			tds[j].innerHTML = arr[i][j];
		}
	}
}
//обработка клика по ячейке
function cellClick(e) {
	var el = e.target || e.srcElement;
	//получаем расоположение ячейки
	var i = el.id.charAt(0);
	var j = el.id.charAt(2);
	//проверяем растояние между ячейко и пустой ячейкой
	if ((i == ei && Math.abs(j-ej) == 1) || (j == ej && Math.abs(i - ei) == 1)) {
		document.getElementById(ei + " " + ej).innerHTML = el.innerHTML;
		el.innerHTML = "";
		//увеличивам счет и записываем в поле
		document.getElementById("score").innerHTML = ++score;
		//проверка на победу
		ei = i;
		ej = j;
		var victory = true;
		for (i = 0; i < 4; ++i) {
			for (j = 0; j < 4; ++j) {
				if (i + j != 6 && document.getElementById(i + " " + j).innerHTML != i*4 + j +1) {
					victory = false;
					break;
				}
			}
		}
		if (victory) {
			example();
		}
	}
	
}

async function clickPost() {
	var val = document.getElementsByTagName("input")[0].value;
	var data = {
		username: val,
		userscore: score
	};
	
	var content = await postData(data);
	var table = document.querySelector('.top-table');
	setTable(content, table);
	
	newGame();
	example();
}
async function postData(data = {}) {
    var response = await fetch('../php/setRecord.php', {
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        method: 'POST',
        body: JSON.stringify(data)
    });
    var result = await response.json();
    return result;
}

function setTable(data, table) {
    table.innerHTML = `
    <tr class="hat">
		<th class="head-column">
			Игрок
		</th>
		<th class="head-column">
			Счет
		</th>
	</tr>
	`
	;
    var i;
    var maxNum = (data.length <= 10) ? data.length : 11;
	for (i = 0; i < maxNum ; i++) {
	    table.innerHTML += 
	    `<tr>
			<td class="user-score_cell">${data[i].name}</td>
			<td class="user-score_cell">${data[i].score}</td>
		</tr>
		`;
	}
}