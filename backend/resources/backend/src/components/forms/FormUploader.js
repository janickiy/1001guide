import React, {useState, useRef} from 'react';
import axios from 'axios';
import {routerUrl, siteUrl} from "../../helpers/client-server";
import Loading from '../Loading';

const FormUploader = ({name, value, initFile, setValue}) => {

  const [file, setFile] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [errorText, setErrorText] = useState(null);

  const imgInput = useRef(null);


  const handleImagePick = () => {
    setIsLoading(true);
    const file = imgInput.current.files[0];
    if ( !file ) return;

    let data = new FormData();
    data.set("file", file);
    data.set("action", "file_upload");

    axios.post(routerUrl, data)
    .then(response => {
      if ( response.data.error )
        setErrorText(response.data.error);
      else {
        setFile(response.data.image);
        setValue(name, response.data.id);
      }
      setIsLoading(false);
    })
    .catch(error => {
      setErrorText(error.toString());
      setIsLoading(false);
    });
  };

  const imagePreview = (
    <div className="image-preview">
      {(file || initFile) ? (<img src={siteUrl + (file || initFile)} alt=""/>) : "Файл не выбран"}
    </div>
  );

  const loadedContent = isLoading ?
    (<Loading/>) :
    errorText || imagePreview;

  return (
    <div className="card image-uploader mb-3">
      <div className="card-body">
        <div className="form-group">
          <label>Выберите файл</label>
          <input type="file" className="form-control-file" ref={imgInput} onChange={handleImagePick} />
          <input type="hidden" name={name} value={value} onChange={setValue} />
        </div>
        {loadedContent}
      </div>
    </div>
  );

};

export default FormUploader;