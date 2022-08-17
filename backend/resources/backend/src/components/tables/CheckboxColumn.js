import React from 'react';

const CheckboxColumn = ({id, handleCheckbox, isChecked}) => {
  return (
    <td className="td-check">
      <div className="form-check">
        <input type="checkbox" className="form-check-input delete-mark" name={id} value="1" onChange={handleCheckbox} checked={isChecked} />
      </div>
    </td>
  );
};

export default CheckboxColumn;