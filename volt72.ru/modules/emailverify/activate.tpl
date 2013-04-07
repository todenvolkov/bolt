{if $errors|@count > 0}
 <h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">Ошибка при регистрации</h1>
	{$errors}
{else}
	<h1 style="width: 100%; font-size: 26px; font-weight: bold; color: gray; font-family: Calibri; clear: both; text-transform: uppenspace; margin-bottom: 8px;">{l s='Registration completed' mod='emailverify'}</h1>
	{l s='Your account has been successfuly activated.' mod='emailverify'}<br />
	{l s='You can now log in to our shop.' mod='emailverify'}
{/if}