import React from 'react';

const FormRadio = ({name, value, label, state, setState}) => {

  const inputId = name+'_'+value;

  return (
    <div className="form-check">
      <input className="form-check-input" type="radio"
             name={name} id={inputId} value={value}
             checked={state === value} onChange={setState}
      />
      <label className="form-check-label" htmlFor={inputId}>
        {label}
      </label>
    </div>
  );

};

export default FormRadio;