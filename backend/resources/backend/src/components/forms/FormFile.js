import React, {useState} from 'react';


const FormFile = ({file, setFile, label="Загрузите файл"}) => {
  return (
    <div className="custom-file">
      <input type="file" className="custom-file-input" id="customFile" onChange={setFile} />
      <label className="custom-file-label" htmlFor="customFile">{label}</label>
    </div>
  );
};


export default FormFile;