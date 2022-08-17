import React from 'react';

const ButtonCopy = ({label, isLoading, lang, handleClick, align="left"}) => {

  const spinner = isLoading ?
    (<span className="spinner-border spinner-border-sm" role="status" aria-hidden="true" />) :
    null;

  const disbaled = isLoading ? true : false;

  const divClassName = `text-${align}`;

  return (
    <div className={divClassName}>
      <p>
        <button type="button" className="btn btn-secondary" disabled={disbaled} onClick={handleClick}>
          {spinner}
          {label}
        </button>
      </p>
    </div>
  );

};

export default ButtonCopy;