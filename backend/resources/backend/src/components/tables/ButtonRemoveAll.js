import React from 'react';

const ButtonRemoveAll = ({handleRemoveAll}) => {
  return (
    <button type="button" className="btn btn-danger" onClick={handleRemoveAll}>Удалить выбранные</button>
  );
};

export default ButtonRemoveAll;