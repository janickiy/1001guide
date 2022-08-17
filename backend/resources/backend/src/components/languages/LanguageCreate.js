import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const LanguageCreate = () => {

  return (
    <div className="create-page">
      <h1>Новый Язык</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="languages" actionType="save" />
    </div>
  );

};

export default LanguageCreate;