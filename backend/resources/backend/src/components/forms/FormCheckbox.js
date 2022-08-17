import React from 'react';

const FormCheckbox = ({name, label, value, setValue}) => {

  const inputId = name+'_'+value;

  return (
    <div className="form-check">
      <input className="form-check-input" type="checkbox"
             name={name} id={inputId} value="1"
             checked={value===1} onChange={setValue}
      />
      <label className="form-check-label" htmlFor={inputId}>
        {label}
      </label>
    </div>
  );

};

export default FormCheckbox;