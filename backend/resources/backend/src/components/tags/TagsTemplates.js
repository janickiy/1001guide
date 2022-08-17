import React, {useState, useEffect} from 'react';
import {sendRequest} from "../../helpers/client-server";
import Loading from "../Loading";
import Form from "../templates/Form";
import ButtonSave from "../forms/ButtonSave";
import LanguagePicker from "../forms/LanguagePicker";
import Tabs from "../includes/Tabs";
import VariableList from "../templates/VariableList";



const TagsTemplates = ({tagId}) => {

  // values
  const [values, setValues] = useState([]);

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

  // Page type (fixed)
  const pageType = "tag";

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
    const response = await sendRequest(`templates/${language}/${pageType}/${field}/${tagId}`);
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
        tag_id: tagId,
        _method: "PUT"
      },
      true
    );
    setIsSaving(false);
  };


  /**
   * Pick Language
   *
   * @param {String} lang
   */
  const pickLanguage = lang => {
    setLanguage(lang);
    window.localStorage.setItem("picked_lang", lang);
  };


  const handleSave = e => {
    e.preventDefault();
    save();
    console.log("saved!");
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


  /**
   * Select the tab
   *
   * @param {*} selected - selected value
   * @param {Function} setSelected - setState function
   * @return {*}
   */
  const selectTab = (selected, setSelected) => {
    // switch the tab
    setSelected(selected);
  };

  /**
   * Select Field
   * @param selected
   */
  const selectField = selected => {
    selectTab(selected, setField);
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
        markAsChanged={()=>{}}
      />

      <ButtonSave
        label="Сохранить"
        isLoading={isSaving}
      />
    </form>;



  // display
  return (
    <div className="row mt-3">
      <div className="col-sm-12">

        <h2>Шаблонные поля тега</h2>

        <LanguagePicker
          setErrorText={()=>{}}
          currentLanguage={language}
          languageUpdate={pickLanguage}
        />

        <Tabs
          current={field}
          setCurrent={selectField}
          links={fields}
          extraClassName="nav-pills"
        />

        <h3>{fields[field]} ({language})</h3>

        <VariableList/>

        {form()}

      </div>
    </div>
  )

};


export default TagsTemplates;