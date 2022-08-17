import React, {useState, useEffect} from 'react';
import {sendRequest} from '../../helpers/client-server';

const LanguagePicker = ({currentLanguage, languageUpdate, setErrorText, match, label="Выберите язык редактирования"}) => {

  // languages list
  const [languageList, setLanguageList] = useState([]);

  // get list of languages
  useEffect(() => {

    sendRequest('languages/codes')
      .then(response => {
        if ( !response.data.items ) {
          setErrorText("Can't get language list");
          return false;
        }
        setLanguageList(response.data.items);
      })
      .catch(error => {
        setErrorText(error.toString());
      });
  }, []);

  const selectOptions = languageList.map((option, key) => {
    return (
      <option value={option} key={key}>{option}</option>
    );
  });

  // on lang change
  const handleChange = e => {
    languageUpdate(e.target.value);
  };

  // build select
  return (
    <div className="card mb-5 mt-3 bg-light">
      <div className="card-header">
        {label}
      </div>
      <div className="card-body">
        <select name="language" id="languages" className="form-control" value={currentLanguage} onChange={handleChange}>
          {selectOptions}
        </select>
      </div>
    </div>
  );

};

export default LanguagePicker;