import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Countries = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["name", "country_code", "total_tours"]}
        columnWithLink={0}
        actions={["edit", "delete", "show"]}
        type="countries"
        multilang={true}
      />
    </div>
  )
};

export default Countries;

