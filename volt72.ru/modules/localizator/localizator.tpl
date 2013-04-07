<fieldset style="width: 300px;float:right;margin-left:15px;">
	<legend><img src="../img/admin/manufacturers.gif" /> Информация</legend>
	<div id="dev_div">
		<span><b>Версия:</b> 1.0</span><br>
		<span><b>Лицензия:</b> <a class="link" href="http://www.opensource.org/licenses/osl-3.0.php" target="_blank">OSL 3.0</a></span><br>
		<span><b>Разработчик:</b> <a class="link" href="mailto:admin@prestalab.ru" target="_blank">ORS</a><br>
                <span><b>Описание:</b> <a class="link" href="http://prestalab.ru/cms.php?id_cms=4" target="_blank">PrestaLab.ru</a><br>
		<p style="text-align:center"><a href="http://prestalab.ru/"><img src="http://prestalab.ru/upload/banner.png" alt="Модули и шаблоны для PrestaShop" /></a></p>
	</div>
</fieldset>
<form action="{$action}" method="post" style="margin-top:10px;">
  <fieldset>
    <legend>
      <img src="../img/admin/prefs.gif">
       Генерация файлов перевода
    </legend>
    <div class="margin-form">
      <p>Создает файлы со строками для перевода, которые можно перевести через админку.</p>
      <p>tab_lang, order_state_lang, country_lang, contact_lang, discount_type_lang, profile_lang, quick_access_lang, order_return_state_lang, meta_lang, carrier_lang, order_message_lang, group_lang</p>
    </div>
    <div class="margin-form">
      <input type="submit" value="   Выполнить   " name="submitTransGen" class="button">
    </div>
  </fieldset>
</form>
<br/>
<form action="{$action}" method="post" style="margin-top:10px;">
  <fieldset>
    <legend>
      <img src="../img/admin/prefs.gif">
       Импорт перевода
    </legend>
    <label>Язык:</label>
    <div class="margin-form">
      <select name="id_lng">
      {foreach from=$langs item=lang}
         <option value="{$lang.id_lang}">{$lang.name}</option>
      {/foreach}
      </select>
      <p>Импорт перевода для выбранного языка. Если перевод не произведен- получите англ. интерфейс.</p>
    </div>
    <div class="margin-form">
      <input type="submit" value="   Выполнить   " name="submitTransLate" class="button">
    </div>
  </fieldset>
</form>
<br/>
<form action="{$action}" method="post" style="margin-top:10px;">
  <fieldset>
    <legend>
      <img src="../img/admin/prefs.gif">
       Региональные настройки
    </legend>
    <label>Язык:</label>
    <div class="margin-form">
      <select name="id_lng">
      {foreach from=$langs item=lang}
         <option value="{$lang.id_lang}">{$lang.name}</option>
      {/foreach}
      </select>
      <p></p>
    </div>

    <label>Очистка регионов:</label>
    <div class="margin-form">
      <input type="radio" name="drop_states" id="drop_states" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_states" id="drop_states" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p></p>
    </div>
    <label>Импорт регионов:</label>
    <div class="margin-form">
      <input type="radio" name="import_states" id="import_states" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="import_states" id="import_states" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p></p>
    </div>
    
    <label>Очистка стран:</label>
    <div class="margin-form">
      <input type="radio" name="drop_countries" id="drop_countries" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_countries" id="drop_countries" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Кроме выбранной</p>
    </div>
    
    <label>Очистка налогов:</label>
    <div class="margin-form">
      <input type="radio" name="drop_taxes" id="drop_taxes" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_taxes" id="drop_taxes" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p></p>
    </div>
    
    <label>Импорт налогов:</label>
    <div class="margin-form">
      <input type="radio" name="import_taxes" id="import_taxes" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="import_taxes" id="import_taxes" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p></p>
    </div>
    
    
    <label>Удаление языков:</label>
    <div class="margin-form">
      <input type="radio" name="drop_lang" id="drop_lang" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_lang" id="drop_lang" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Кроме выбранного</p>
    </div>
    <div class="margin-form">
      <input type="submit" value="   Выполнить   " name="submitRegional" class="button">
    </div>
  </fieldset>
</form>
<br/>
<form action="{$action}" method="post" style="margin-top:10px;">
  <fieldset>
    <legend>
      <img src="../img/admin/prefs.gif">
       Очистка базы
    </legend>
    <label>Продукты:</label>
    <div class="margin-form">
      <input type="radio" name="drop_products" id="drop_products" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_products" id="drop_products" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Очистка продуктов, категорий, комбинаций, свойств, тегов, картинок, сцен</p>
    </div>
    <label>Заказы:</label>
    <div class="margin-form">
      <input type="radio" name="drop_orders" id="drop_orders" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_orders" id="drop_orders" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Очистка заказов, клиентов, корзин, гостей, сообщений</p>
    </div>
    
    <label>Поставщики и производители:</label>
    <div class="margin-form">
      <input type="radio" name="drop_mansup" id="drop_mansup" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_mansup" id="drop_mansup" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Очистка поставщиков, производителей</p>
    </div>
    
    <label>Разное:</label>
    <div class="margin-form">
      <input type="radio" name="drop_other" id="drop_other" value="1" checked="checked">
      <label class="t">
        <img src="../img/admin/enabled.gif" alt="">
         Да
      </label>
      <input type="radio" name="drop_other" id="drop_other" value="0">
      <label class="t">
        <img src="../img/admin/disabled.gif" alt="">
         Нет
      </label>
      <p>Очистка соединений, статистики, поискового индекса, магазинов</p>
    </div>

    <div class="margin-form">
      <input type="submit" value="   Выполнить   " name="submitPrepare" class="button">
    </div>
  </fieldset>
</form>