import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CodeCreate = () => {

  return (
    <div className="create-page">
      <h1>Новый Код</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="codes" actionType="save" />
    </div>
  );

};

export default CodeCreate;