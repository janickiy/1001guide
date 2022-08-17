import React from 'react';

const FormHidden = ({name, value}) => {

  return (
    <input type="hidden" name={name} value={value} />
  );

};

export default FormHidden;