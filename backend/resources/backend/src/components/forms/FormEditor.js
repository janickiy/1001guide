import React from 'react';
import CKEditor from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const FormEditor = ({
  name, label, value, setValue, changedFieldFlag=false, markAsNoChanged=null
}) => {

  const changedFieldBtn = changedFieldFlag ?
    <button type="button" onClick={markAsNoChanged} className="btn">
      <i className="far fa-edit"/> изменено вручную
    </button>:
    null;

  return (
    <div className="form-group">
      {
        label ? <label>{label}</label>: null
      }
      <CKEditor
        name={name}
        editor={ ClassicEditor }
        data={value}
        onChange={ ( event, editor ) => {
          setValue(name, editor.getData());
        } }
      />
      {changedFieldBtn}
    </div>
  );

};

export default FormEditor;