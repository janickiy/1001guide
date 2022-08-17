import React from 'react';

const CheckAllCheckbox = ({handleAllChecked}) => {
  return (
    <p><a href="/remove/" className="check-all check-all-link" onClick={handleAllChecked}>Отметить все</a></p>
  );
};

export default CheckAllCheckbox;