import React from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";
import IataImport from './IataImport';

const Iatas = () => {
  return (
    <div>
      <IataImport />
      <ButtonAdd />
      <ItemList
        tableData={["iata", "place"]}
        columnWithLink={0}
        actions={["edit", "delete"]}
        type="iata"
      />
    </div>
  )
};

export default Iatas;

