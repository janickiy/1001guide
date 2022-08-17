import React, {useState, useEffect} from 'react';
import LanguagePicker from "../forms/LanguagePicker";
import Tabs from "../includes/Tabs";
import VariableList from "./VariableList";
import Form from "./Form";
import ButtonSave from "../forms/ButtonSave";
import Loading from "../Loading";
import {sendRequest} from "../../helpers/client-server";

const Templates = () => {

  // page types
  const pageTypes = {
    country: "Страна",
    location: "Город",
    poi: "Достопримечательность",
  };
  const [pageType, setPageType] = useState("country");

  // fields
  const fields = {
    title: "Заголовок",
    announce: "Текст сверху",
    title_bottom: "Заголовок снизу",
    content: "Текст снизу",
    meta_description: "META Description"
  };
  const wysiwygFields = ["announce", "content"];
  const textareaFields = ["meta_description"];
  const [field, setField] = useState("title");

  // changed
  const [isChanged, setIsChanged] = useState(false);

  /**
   * Array of field values.
   * Ex.:
   * [
   *  {
   *    id,
   *    frontendId,
   *    value
   *  }
   * ]
   */
  const [values, setValues] = useState([]);

  // errors
  const [errorText, setErrorText] = useState(null);
  const [messageText, setMessageText] = useState(null);

  // language
  const [language, setLanguage] = useState(
    window.localStorage.getItem("picked_lang") || "en"
  );

  // loading
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);


  // load values
  useEffect(()=> {
    loadValues();
  }, [field, pageType, language]);



  /**
   * Load values from the Server
   *
   * @return {Promise<void>}
   */
  const loadValues = async () => {
    setIsLoading(true);
    const response = await sendRequest(`templates/${language}/${pageType}/${field}`);
    console.log(response);
    setValues(
      response.data.items && response.data.items.length ?
        response.data.items.map((fieldValue, index) => {
          fieldValue.frontendId = index;
          return fieldValue;
        }):
        []
    );
    setIsLoading(false);
  };


  /**
   * Save values on current page
   *
   * @return {Promise<void>}
   */
  const save = async () => {
    setIsSaving(true);
    const response = await sendRequest(
      `templates/${language}/${pageType}/${field}`,
      'post',
      {
        values,
        _method: "PUT"
      },
      true
    );
    setIsSaving(false);
    setIsChanged(false);
  };


  /**
   * Show confirmation window: save or not
   */
  const confirmToSave = () => {
    if (window.confirm('Сохранить изменения?')) {
      save();
    } else {
      setIsChanged(false);
    }
    setIsChanged(false);
  };


  /**
   * Pick Language
   *
   * @param {String} lang
   */
  const pickLanguage = lang => {
    // ask for save
    if ( isChanged )
      confirmToSave();

    // set language
    setLanguage(lang);
    window.localStorage.setItem("picked_lang", lang);
  };


  /**
   * Select the tab
   *
   * @param {*} selected - selected value
   * @param {Function} setSelected - setState function
   * @return {*}
   */
  const selectTab = (selected, setSelected) => {
    // ask for save
    if ( isChanged )
      confirmToSave();

    // switch the tab
    setSelected(selected);
  };

  /**
   * Select Page Type
   * @param selected
   */
  const selectPageType = selected => {
    selectTab(selected, setPageType);
  };

  /**
   * Select Field
   * @param selected
   */
  const selectField = selected => {
    selectTab(selected, setField);
  };


  /**
   * Detect field type
   *
   * @param {String} field
   * @return {string}
   */
  const detectFieldType = field => {
    if ( wysiwygFields.includes(field) )
      return "editor";
    if ( textareaFields.includes(field) )
      return "textarea";
    return "text";
  };


  const markAsChanged = () => {
    if ( isChanged ) return;
    setIsChanged(true);
  };


  const handleSave = e => {
    e.preventDefault();
    save();
    console.log("saved!");
  };


  const form = () => isLoading?
    <Loading/>:
    <form onSubmit={handleSave}>
      <Form
        name={field}
        label={fields[field]}
        values={values}
        setValues={setValues}
        type={detectFieldType(field)}
        markAsChanged={markAsChanged}
      />

      <ButtonSave
        label="Сохранить"
        isLoading={isSaving}
      />
    </form>;


  return (
    <div>

      <LanguagePicker
        setErrorText={setErrorText}
        currentLanguage={language}
        languageUpdate={pickLanguage}
      />

      <Tabs
        current={pageType}
        setCurrent={selectPageType}
        links={pageTypes}
      />

      <Tabs
        current={field}
        setCurrent={selectField}
        links={fields}
        extraClassName="nav-pills"
      />

      <h2>{pageTypes[pageType]}: {fields[field]} ({language})</h2>

      <VariableList/>

      {form()}

    </div>
  )
};

export default Templates;

