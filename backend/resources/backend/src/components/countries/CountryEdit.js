import React from 'react';
import FormEdit from '../forms/FormEdit';
import {fieldsToShow} from './fields';

const CountryEdit = ({match}) => {
  return (
    <div className="edit-page">
      <h1>Редактирование страны</h1>
      <FormEdit
        fieldsToShow={fieldsToShow}
        itemType="countries"
        itemId={Number(match.params.id)}
        multilang={true}
        trackChanges={true}
      />
    </div>
  );

};

export default CountryEdit;