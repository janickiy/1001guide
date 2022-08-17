import React, {useState, useEffect} from 'react';
import LanguagePicker from "../forms/LanguagePicker";
import FormSelect from "../forms/FormSelect";
import FormFile from "../forms/FormFile";
import Loading from "../Loading";
import MessageBlock from "../MessageBlock";
import {routerUrl} from "../../helpers/client-server";
import axios from 'axios';


const Import = () => {

  // loading
  const [isLoading, setIsLoading] = useState(false);

  // message
  const [messageText, setMessageText] = useState(null);

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

  // file
  const [file, setFile] = useState(null);


  const handlePageTypeChange = e => {
    setPageType(e.target.value);
  };


  const handleFilePick = e => {
    setFile(e.target.files[0]);
    setMessageText(null);
  };


  const uploadFile = async () => {
    setIsLoading(true);
    const url = `${routerUrl}import/${language}/${pageType}`;
    const formData = new FormData();
    formData.append('xls', file);
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      }
    };
    const response = await axios.post(url, formData,config);
    console.log(response);
    setIsLoading(false);
    setMessageText("Импорт завершён");
  };


  const handleClick = () => {
    uploadFile();
  };


  const message = messageText ?
    <div className="row">
      <div className="col-sm-12">
        <MessageBlock>{messageText}</MessageBlock>
      </div>
    </div> :
    null;


  return (
    <>
      <div className="row">
        <div className="col-sm-12">
          <h2>Импорт</h2>
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
          <FormFile
            file={file}
            setFile={handleFilePick}
          />
        </div>
      </div>

        <div className="row">
          <div className="col-sm-12 text-right">
            {
              isLoading ?
                <Loading/>:
                <button className="btn btn-primary" onClick={handleClick} disabled={!Boolean(file)}>
                  Импорт
                </button>
            }
          </div>
        </div>

        {message}
    </>
  );

};


export default Import;