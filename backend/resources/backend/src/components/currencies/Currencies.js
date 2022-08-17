import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";

const Currencies = () => {
  return (
    <div>
      <ButtonAdd />
      <ItemList
        tableData={["code", "sign"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="currencies"
      />
    </div>
  )
};

export default Currencies;

