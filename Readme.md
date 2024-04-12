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

В formajax.js указать путь до index.php скрипта. Все!
В index.php поменять имя, имеется ввиду "от кого"(fromname) иначе так и приходит "Имя"
Обязательно указать для формы. 
```
data-formajax="" method="post" 
```
По data-formajax  цепляется отправка формы

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

12.04.2024

Удалил версию PHPMailer которая была в исходном репозитории, так как она не подходила для пхп 8.

+ Через композер обновил PHPMailer что бы поддерживался PHP 8. 
```
composer require phpmailer/phpmailer
```
+ Обновил деприкейтед в formajax.php