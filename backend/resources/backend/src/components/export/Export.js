import React, {useState} from 'react';
import LanguagePicker from "../forms/LanguagePicker";
import FormSelect from "../forms/FormSelect";
import {routerUrl} from "../../helpers/client-server";


const Export = () => {

  // language
  const [language, setLanguage] = useState(
    window.localStorage.getItem("picked_lang") || "en"
  );
  const [errorText, setErrorText] = useState(false);

  // page type
  const pageTypes = [
    {name: "country", value: "Страны"},
    {name: "city", value: "Города"},
    {name: "poi", value: "Достопримечательности"},
  ];
  const [pageType, setPageType] = useState("country");


  const handlePageTypeChange = e => {
    setPageType(e.target.value);
  };


  const handleClick = () => {
    const exportFileUrl = `${routerUrl}export/${language}/${pageType}`;
    window.open(exportFileUrl);
  };


  return (
    <>
      <div className="row">
        <div className="col-sm-12">
          <h2>Экспорт</h2>
        </div>
      </div>

      <div className="row">
        <div className="col-sm-6">
          <LanguagePicker
            label="Язык"
            currentLanguage={language}
            languageUpdate={setLanguage}
            setErrorText={setErrorText}
          />
        </div>
        <div className="col-sm-6">
          <FormSelect
            label="Тип страниц"
            name="type"
            variants={pageTypes}
            value={pageType}
            setValue={handlePageTypeChange}
          />
          <button className="btn btn-primary btn-lg btn-block" onClick={handleClick}>
            Экспорт
          </button>
        </div>
      </div>
    </>
  );

};


export default Export;