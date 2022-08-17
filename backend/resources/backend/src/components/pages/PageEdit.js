import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const PageEdit = ({match}) => {
  return (
    <div className="edit-page">
      <h1>Редактирование языка</h1>
      <FormEdit fieldsToShow={fieldsToShow} itemType="pages" itemId={Number(match.params.id)} multilang={true} />
    </div>
  );

};

export default PageEdit;