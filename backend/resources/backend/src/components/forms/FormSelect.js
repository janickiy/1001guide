import React from 'react';

const FormSelect = ({name, label, value, setValue, variants, disabled=false}) => {

  const options = variants.map((variant, key) => {
    return (
      <option value={variant.name} key={key}>{variant.value}</option>
    );
  });

  return (
    <div className="form-group">
      <label>{label}</label>
      <select className="form-control" name={name} value={value} onChange={setValue} disabled={disabled}>
        {options}
      </select>
    </div>
  );

};

export default FormSelect;