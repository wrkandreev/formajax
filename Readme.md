# FormAjax на php8
За основу взят этот репозиторий: https://github.com/milkamil93/FormAjax

## Использование
```
<form data-formajax="Произвольная тема" data-to="произвольные email,s через запятую">
  <div class="form-group">
    <label>Телефон</label>
    <input class="form-control" type="tel" name="phone" data-name="Телефон" data-error="Заполните телефон">
  </div>
  <button class="btn btn-primary">Отправить</button>
</form>
```
+ По data-formajax цепляется отправка формы: если этот атрибут не прописан магии не будет.
+ Прописать data-to к форме или в index.php. Можно несколько email, через запятую.
+ Прописать data-name и data-error к инпутам формы.
+ Подключить formajax.js как скрипт в html с формой. 
+ В formajax.js указать путь до index.php скрипта.
+ В index.php поменять имя, имеется ввиду "от кого"(fromname). 

Всё!

## Дефолтные CSS на всякий случай
```
form .alert {
    padding: 5px 0 10px;
    font-size: 20px;
}

form .alert-error, form .invalid-feedback {
    color: red;
}

form .alert-success {
    color: #009640;
}
button:disabled {
	cursor: default;
    background-color: #e3e3e3;
}
button:disabled:hover {
    background-color: #e3e3e3!important;
}
```

## История изменений

Удалил версию PHPMailer которая была в исходном репозитории, через композер обновил для поддержки PHP 8. Переписал деприкейтед методы. 
```
composer require phpmailer/phpmailer
```

## TODO

- [ ] Проверить отправку файлов
- [ ] Проверить отправку через SMTP 