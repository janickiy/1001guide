import React, {useState, useEffect} from 'react';
import FormInput from './FormInput';
import FormTextarea from './FormTextarea';
import FormEditor from './FormEditor';
import FormCheckbox from './FormCheckbox';
import FormSelect from './FormSelect';
import FormHidden from './FormHidden';
import FormImage from './FormImage';
import ButtonSave from './ButtonSave';
import LanguagePicker from './LanguagePicker';
import ErrorBlock from '../ErrorBlock';
import MessageBlock from '../MessageBlock';
import BackButton from '../includes/BackButton';
import {sendRequest} from "../../helpers/client-server";
import { Redirect } from 'react-router-dom'
import {getFieldType} from '../../helpers/forms';
import {adminUrl} from "../../config";

const FormEdit = ({
  fieldsToShow, itemType, itemId=null, multilang=false, extraParams=null,
  trackChanges=false
}) => {

  const [fields, setFields] = useState(fieldsToShow);

  const initValues = fieldsToShow.reduce((obj, item) => {
    obj[item.name] = (item.hasOwnProperty("variants")) ? item.variants[0].name : "";
    return obj;
  }, {});

  // input values
  const [values, setValues] = useState( initValues );

  // changed fields
  const [changedFields, setChangedFields] = useState([]);

  // errors
  const [errorText, setErrorText] = useState(null);
  const [messageText, setMessageText] = useState(null);

  // loading
  const [isLoading, setIsLoading] = useState(false);

  // language
  const [language, setLanguage] = useState(
    window.localStorage.getItem("picked_lang") || "en"
  );

  // redirect
  const [redirect, setRedirect] = useState(null);


  // get field type if exists
  useEffect(() => {
    if ( !extraParams || !extraParams.hasOwnProperty('group_id') )
      return;

    getFieldType(extraParams['group_id'])
    .then(fieldType => {

      if ( !fieldType )
        return;

      setFields(fields => {
        return fields.reduce( (acc, field) => {
          let addingField = ( field.name === "content" ) ?
            {...field, type: fieldType} :
            field;
          acc.push(addingField);
          return acc;
        }, [] );
      });

    });

  }, []);


  // get initial data from server
  useEffect(() => {

    if ( !itemId )
      return;

    // loading...
    setIsLoading(true);

    // make request properties

    // send request
    sendRequest(`${itemType}/${itemId}?lang=${language}`, 'get', {lang: language})
      .then(response => {

        // if object not found
        if ( !response.data.item ) {
          setErrorText(`Object with ID ${itemId} was not found`);
          return;
        }

        // merge field properties
        const item = response.data.item;
        let newValues = Object.assign({}, initValues);
        Object.keys(item).forEach(key => {
          if ( !newValues.hasOwnProperty(key) || !item[key] )
            return;
          newValues[key] = item[key];
        });

        // update form fields
        setValues(newValues);
        setIsLoading(false);

        // set changed fields
        if ( trackChanges && item.changed_fields )
          setChangedFields(item.changed_fields.split(','));

        console.log(response);

      })
      // error
      .catch(error => {
        setIsLoading(false);
        setErrorText(error.toString());
      });



  }, [language]);


  const resetMessages = () => {
    setMessageText(null);
    setErrorText(null);
  };


  const pickLanguage = lang => {
    setLanguage(lang);
    window.localStorage.setItem("picked_lang", lang);
  };


  // make a redirect
  const renderRedirect = redirect ?
    (<Redirect to={redirect} />) :
    null;

  // build a link to "Edit" page
  const generateEditLink = itemId => {
    const urlPart = window.location.href.split(adminUrl);
    return adminUrl + urlPart[1].replace('/create/', '/'+itemId+'/edit/');
  };


  const simplySetValue = (name, value) => {
    // mark field as changed
    if ( trackChanges && !changedFields.includes(name) )
      setChangedFields([...changedFields, name]);

    // set value
    return setValues({
      ...values,
      [name]: value
    });
  };

  // on inputs change
  const handleInputChange = e => {
    const target = e.target;
    simplySetValue(target.name, target.value);
  };

  // on editor change
  const handleEditorChange = (name, value) => {
    if ( isLoading )
      return;
    simplySetValue(name, value);
  };

  // on checkbox change
  const handleCheckboxChange = e => {
    const target = e.target;
    const checked = Number(target.checked);
    simplySetValue(target.name, checked);
  };


  // on save/update
  const handleSubmit = e => {
    e.preventDefault();

    setIsLoading(true);

    // prepare data
    const url = itemType + ( itemId ? `/${itemId}` : '' );
    let dataToSend = Object.assign(
      values,
      itemId ? {'_method': 'PUT'} : {}
    );

    if ( multilang )
      dataToSend = Object.assign(dataToSend, {lang: language});

    if ( extraParams )
      dataToSend = Object.assign(dataToSend, extraParams);

    // preview tracked changes
    if ( trackChanges )
      dataToSend.changed_fields = changedFields.join(',');

    // reset messages
    resetMessages();

    console.log(dataToSend);

    // send to a server
    sendRequest(url, "post", dataToSend)
      .then(response => {

        setIsLoading(false);

        console.log('response', response);

        const data = response.data;

        // if some required fields are empty
        if ( data.hasOwnProperty("error") ) {
          setErrorText(
            data.error
          );
          return false;
        }

        // if some required fields are empty
        if ( data.hasOwnProperty("required") ) {
          setErrorText(
            "Обязательные поля не заполнены: " + data.required.join(', ')
          );
          return false;
        }

        // if it's okay
        // on save new item
        if ( !itemId ) {
            console.log('new itemId', itemId);
          if ( !data.hasOwnProperty("id") ) {
            return setErrorText("Не удалось создать элемент");
          }
          const editLink = generateEditLink(data.id);
          setRedirect(editLink);
        }
        // on update existing
        else {
          if ( data.hasOwnProperty("success") )
            setMessageText(data.success);
        }
      })
      // request error
      .catch(error => {
          console.log('catch error', error);
        setIsLoading(false);
        setErrorText(error.toString());
      });
  };


  // build form fields from list
  const formFields = fields.map( (field, key) => {

    const changedFieldFlag= trackChanges && changedFields.includes(field.name);

    const markAsNoChanged = trackChanges ?
      () => setChangedFields(
        changedFields.filter(changedField => changedField !== field.name)
      ):
      null;

    switch (field.type) {

      // <textarea>
      case "textarea":
        return (
          <FormTextarea
            key={key}
            name={field.name}
            label={field.title}
            value={values[field.name]}
            setValue={handleInputChange}
            changedFieldFlag={changedFieldFlag}
            markAsNoChanged={markAsNoChanged}
          />
        );

      // WYSIWYG
      case "editor":
        return (
          <FormEditor
            key={key}
            name={field.name}
            label={field.title}
            value={values[field.name]}
            setValue={handleEditorChange}
            changedFieldFlag={changedFieldFlag}
            markAsNoChanged={markAsNoChanged}
          />
        );

      // <input type="checkbox">
      case "checkbox":
        return (
          <FormCheckbox
            key={key}
            name={field.name}
            label={field.title}
            value={values[field.name]}
            setValue={handleCheckboxChange}
          />
        );

      // <select>
      case "select":
        return (
          <FormSelect
            key={key}
            name={field.name}
            label={field.title}
            value={values[field.name]}
            setValue={handleInputChange}
            variants={field.variants}
          />
        );

      // input[type="hidden"]
      case "hidden":
        return (
          <FormHidden
            key={key}
            name={field.name}
            value={values[field.name]}
          />
        );

      // image uploader
      case "image":
        return (
          <FormImage
            key={key}
            name={field.name}
            value={values[field.name]}
            initImage={values["img"]}
            size="197x116"
            setValue={simplySetValue}
          />
        );

      case "none":
        break;

      // input[type="text"]
      default:
        return (
          <FormInput
            key={key}
            name={field.name}
            label={field.title}
            value={values[field.name]}
            setValue={handleInputChange}
            changedFieldFlag={changedFieldFlag}
            markAsNoChanged={markAsNoChanged}
          />
        );

    }


  } );



  // if messages show message block
  const messageBlock = () => {
    const error = (errorText) ? (<ErrorBlock>{errorText}</ErrorBlock>) : null;
    const message = (messageText) ? (<MessageBlock>{messageText}</MessageBlock>) : null;
    return (
      <div>
        {error}
        {message}
      </div>
    );
  };


  return (
    <div>
      {renderRedirect}
      <BackButton/>
      {messageBlock()}
      <form action="/bnd/" onSubmit={handleSubmit}>
        {(multilang) ?
          (<LanguagePicker setErrorText={setErrorText} currentLanguage={language} languageUpdate={pickLanguage} />) :
          null
        }
        {formFields}
        {messageBlock()}
        <ButtonSave label="Сохранить" isLoading={isLoading} />
      </form>
    </div>
  );

};

export default FormEdit;
