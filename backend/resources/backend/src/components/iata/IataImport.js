import React, {useState} from 'react';
import axios from 'axios';
import Loading from '../Loading';
import ErrorBlock from '../ErrorBlock';
import MessageBlock from '../MessageBlock';
import {sendRequest} from '../../helpers/client-server';

const IataImport = () => {

  const [isLoading, setIsLoading] = useState(false);
  const [errorText, setErrorText] = useState(null);
  const [messageText, setMessageText] = useState(null);


  const uploadFile = file => {
    sendRequest('iata/import', 'post', {xls: file})
      .then(response => {
        console.log(response);
        setIsLoading(false);
        if ( response.data.success ) {
          setMessageText(response.data.success);
          setTimeout(()=>window.location.reload(), 3000);
        }
        else if ( response.data.error ) {
          setErrorText(response.data.error);
        }
        else {
          setErrorText('Ошибка загрузки');
        }
      });
  };


  const onChangeHandler = event => {
    const file = event.target.files[0];
    if ( !file ) return;
    setIsLoading(true);
    uploadFile(file);
  };

  const fileInput = () => {
    return (
      <div className="custom-file">
        <input type="file" className="custom-file-input" id="customFile" onChange={onChangeHandler} />
        <label className="custom-file-label" htmlFor="customFile" accept=".xls,.xlsx">Import XLS(X)</label>
      </div>
    )
  };

  return (
    <div className="iata-import mb-5">
      {(errorText) ? (<ErrorBlock>{errorText}</ErrorBlock>) : null}
      {(messageText) ? (<MessageBlock>{messageText}</MessageBlock>) : null}
      {
        isLoading ?
          (<Loading/>) :
          fileInput()
      }
    </div>
  );
};

export default IataImport;