import React from 'react';

const FormTextarea = ({
  name, label, value, setValue, changedFieldFlag=false, markAsNoChanged=null
}) => {

  const changedFieldBtn = changedFieldFlag ?
    <button type="button" onClick={markAsNoChanged} className="btn">
      <i className="far fa-edit"/> изменено вручную
    </button>:
    null;

  return (
    <div className="form-group">
      <label>{label}</label>
      <textarea className="form-control" name={name} value={value} placeholder={label} onChange={setValue} />
      {changedFieldBtn}
    </div>
  );

};

export default FormTextarea;