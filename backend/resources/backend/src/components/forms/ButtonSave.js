import React from 'react';

const ButtonSave = ({label, isLoading, align="right"}) => {

  const spinner = isLoading ?
    (<span className="spinner-border spinner-border-sm" role="status" aria-hidden="true" />) :
    null;

  const disbaled = isLoading ? true : false;

  const divClassName = `text-${align}`;

  return (
    <div className={divClassName}>
      <p>
        <button type="submit" className="btn btn-success" disabled={disbaled}>
          {spinner}
          {label}
        </button>
      </p>
    </div>
  );

};

export default ButtonSave;