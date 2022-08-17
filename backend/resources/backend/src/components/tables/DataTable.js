import React, {useState, useEffect} from 'react';
import CheckboxColumn from './CheckboxColumn';
import CheckAllCheckbox from './CheckAllCheckbox';
import ButtonRemoveAll from './ButtonRemoveAll';
import ActionIcons from './ActionIcons';
import {generateActionLinks} from '../../helpers/tables';
import {sendRequest} from "../../helpers/client-server";
import {Link} from 'react-router-dom';



const DataTable = ({listItems, tableData, columnWithLink, actions=["edit", "delete"], setError, setIsLoading, setListItems, type}) => {

  // checkbox states
  const [checkboxes, setCheckboxes] = useState({});
  const [checkedAll, setCheckedAll] = useState(false);
  useEffect(() => {
    if ( Object.entries(listItems).length !== 0 ) {
      setCheckboxes(()=>{
        let checkboxesObject = {};
        listItems.forEach(item => {
          checkboxesObject[item.id] = false;
        });
        return checkboxesObject;
      });
    }
  }, [listItems] );

  // if no elements to show
  if ( Object.entries(listItems).length === 0 && listItems.constructor === Object ) {
    return (
      <div className="text-center">
        Нет элементов для показа
      </div>
    );
  }


  // handle checkbox click
  const handleCheckbox = (e) => {
    const target = e.target;
    const value = target.checked;
    const name = target.name;
    setCheckboxes(
      {
        ...checkboxes,
        [name]: value
      }
    );
  };


  // handle all checkbox click
  const handleAllChecked = e => {
    e.preventDefault();
    const newCheckedAll = !checkedAll;
    setCheckboxes( () => {
      let newCheckboxes = {};
      Object.keys(checkboxes).forEach(function (item) {
        newCheckboxes[item] = newCheckedAll;
      });
      return newCheckboxes;
    } );
    setCheckedAll(newCheckedAll);
  };


  // remove all
  const handleRemoveAll = e => {
    e.preventDefault();
    setIsLoading(true);

    let idsToRemove = [],
        idsToStay = [];
    Object.keys(checkboxes).forEach(function (id) {
      if ( checkboxes[id] === true )
        idsToRemove.push(Number(id));
      else
        idsToStay.push(Number(id));
    });

    sendRequest(`${type}/multiple`, 'post', {_method: "DELETE", ids: idsToRemove.join(",")})
      .then(response => {
        console.log(response);

        // on error
        if ( response.data.hasOwnProperty('error') ) {
          setError(response.data.error);
          setIsLoading(false);
          return false;
        }

        // remove items from list
        setListItems(listItems.filter( item => idsToStay.includes(Number(item.id)) ));
        setIsLoading(false);
      });
  };


  // remove one item by ID
  const handleRemove = id => {
    setIsLoading(true);

    sendRequest(`${type}/${id}`, 'post', {_method: "DELETE"})
      .then(response => {
        console.log(response);

        // on error
        if ( response.data.hasOwnProperty('error') ) {
          setError(response.data.error);
          setIsLoading(false);
          return false;
        }

        // remove items from list
        setListItems(listItems.filter(item => item.id !== id));
        setIsLoading(false);
      });
  };


  // choose link in title: editing or viewing
  const titleLinkType = (actions.includes("show")) ? "show" : "edit";

  // rows
  let rows = listItems.map( (item, rowIndex) => {

    if ( Object.entries(checkboxes).length === 0 ) {
      return null;
    }

    const links = generateActionLinks(actions, item.id, item);

    // cols
    let cols = tableData.map( (field, columnIndex) => {
      return (
        <td key={columnIndex}>{ (columnIndex === columnWithLink) ? (
          <Link to={links[titleLinkType]}>{item[field]}</Link>
        ) : item[field] }</td>
      );
    } );

    const checkboxCol = actions.includes("delete") ?
      <CheckboxColumn id={item.id} handleCheckbox={handleCheckbox} isChecked={checkboxes[item.id]} /> :
      null;

    return (
      <tr key={rowIndex}>
        {checkboxCol}
        {cols}
        <ActionIcons
          edit={links.edit}
          view={links.view}
          remove={
            actions.includes("delete") ?
              handleRemove:
              null
          }
          id={item.id}
        />
      </tr>
    );
  } );


  const checkAllRow = actions.includes("delete") ? (
    <div className="row">
      <div className="col-sm-6"><CheckAllCheckbox handleAllChecked={handleAllChecked} /></div>
      <div className="col-sm-6 text-right"><ButtonRemoveAll handleRemoveAll={handleRemoveAll} /></div>
    </div>
  ) : null;


  return (
    <div>
      {checkAllRow}
      <table className="table table-striped items-table">
        <tbody>
        {rows}
        </tbody>
      </table>
    </div>
  );

};

export default DataTable;