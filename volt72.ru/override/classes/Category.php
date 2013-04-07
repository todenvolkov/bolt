<?php

class Category extends CategoryCore
{
    public function getTranslationsFieldsChild() {
        self::validateFieldsLang();
        
        $fieldsArray = array('name', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description');
        $fields = array();
        $languages = Language::getLanguages();
        $defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
        foreach ($languages as $language)
        {
            $fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
            $fields[$language['id_lang']][$this->identifier] = intval($this->id);
            $fields[$language['id_lang']]['description'] = (isset($this->description[$language['id_lang']])) ? Tools::htmlentitiesDecodeUTF8(pSQL($this->description[$language['id_lang']], true)) : '';
            foreach ($fieldsArray as $field)
            {
                if (!Validate::isTableOrIdentifier($field))
                    die(Tools::displayError());

                /* Check fields validity */
                if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']]);
                elseif (in_array($field, $this->fieldsRequiredLang))
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
                else
                    $fields[$language['id_lang']][$field] = '';
            }
        }
        return $fields;
    } 
}
