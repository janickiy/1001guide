import React, {useState, useEffect} from 'react';
import Loading from './Loading';
import ErrorBlock from './ErrorBlock';
import {sendRequest} from '../helpers/client-server';
import LanguagePicker from './forms/LanguagePicker';
import ButtonSave from './forms/ButtonSave';
import MessageBlock from './MessageBlock';
import FormEditor from "./forms/FormEditor";


const LocalSettings = () => {

  const [values, setValues] = useState({});
  const [language, setLanguage] = useState(
    window.localStorage.getItem("picked_lang") || "en"
  );
  const [isLoading, setIsLoading] = useState(true);
  const [errorText, setErrorText] = useState(false);
  const [messageText, setMessageText] = useState(null);


  useEffect(() => {

    setIsLoading(true);

    // get settings
    sendRequest(`settings?lang=${language}`, 'get')
    .then(response => {

      if ( !response.data.items ) return;
      const items = response.data.items;

      // convert items to object
      const loadedValues = Object.keys(items).reduce((obj, key) => {
        return Object.assign(obj, {
          [items[key].name]: {
            value: items[key].value || '',
            name: items[key].id,
            title: items[key].title,
            type: items[key].type
          }
        });
      }, {});

      setValues(values => {
        return {
          ...values,
          ...loadedValues
        }
      });

      setIsLoading(false);

    });

  }, [language]);


  const handleChange = e => {
    const target = e.target;
    setValues({
      ...values,
      [target.name]: {
        ...values[target.name],
        value: target.value
      }
    });
  };


  const handleEditorChange = (name, value) => {
    setValues({
      ...values,
      [name]: {
        ...values[name],
        value: value
      }
    });
  };


  const handleSubmit = e => {
    e.preventDefault();
    setIsLoading(true);

    const dataToSend = {
      item_type: 'local_settings',
      lang: language,
      ...Object.keys(values).reduce((obj, key) => {
        return Object.assign(obj, {
          [key]: values[key].value
        });
      }, {})
    };
    sendRequest('settings', 'post', dataToSend)
      .then(response => {
        setIsLoading(false);
        setMessageText("Изменения сохранены");
      });
  };


  const pickLanguage = lang => {
    setLanguage(lang);
    window.localStorage.setItem("picked_lang", lang);
  };


  const showInput = fieldName => {
    const field = values[fieldName];
    switch (field["type"]) {
      case "editor":
        return (
          <FormEditor
            setValue={handleEditorChange}
            name={fieldName}
            value={field["value"]}
          />
        );
      case "textarea":
        return (
          <textarea
            type="text" className="form-control"
            name={fieldName}
            value={field["value"]}
            placeholder={field["title"]}
            onChange={handleChange}
            onClick={e => {console.log(values)}}
          />
        );
      default:
        return (
          <input
            type="text" className="form-control"
            name={fieldName}
            value={field["value"]}
            placeholder={field["title"]}
            onChange={handleChange}
            onClick={e => {console.log(values)}}
          />
        );
    }
  };


  const settingsList = Object.keys(values).map((name, key) => {
    return (
      <div className="form-group" key={key}>
        <label>{values[name]["title"]}</label>
        {showInput(name)}
      </div>
    );
  });


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
    <div className="local-settings">
      <LanguagePicker setErrorText={setErrorText} currentLanguage={language} languageUpdate={pickLanguage} />
      {messageBlock()}
      <form action="" onSubmit={handleSubmit}>
        {settingsList}
        <ButtonSave label="Сохранить" isLoading={isLoading} />
      </form>
    </div>
  );

};

export default LocalSettings;