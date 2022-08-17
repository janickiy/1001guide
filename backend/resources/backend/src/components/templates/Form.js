import React, {useState, useEffect} from 'react';
import FormInput from "../forms/FormInput";
import FormEditor from "../forms/FormEditor";
import FormTextarea from "../forms/FormTextarea";


const Form = ({values, setValues, name, label, type="text", markAsChanged}) => {


  useEffect(()=> {
    console.log(values);
    if ( !values.length ) {
      addValue();
    }
  }, [values]);


  /**
   * Add new Input Field
   */
  const addValue = () => {
    const frontendId = values.length ? values[values.length-1].frontendId+1 : 1;
    setValues([...values, {
      value: "",
      frontendId,
      id: null
    }]);
  };


  /**
   * Update value in Input Field
   *
   * @param {Number} frontendId - field identification
   * @param {String} newValue
   */
  const updateValue = (frontendId, newValue) => {
    markAsChanged();
    setValues(
      values.map(oldValue => {
        if ( oldValue.frontendId !== frontendId )
          return oldValue;
        return {
          value: newValue,
          id: oldValue.id,
          frontendId
        };
      })
    )
  };


  /**
   * Remove Input Value
   *
   * @param {Number} frontendId
   */
  const removeValue = frontendId => {
    markAsChanged();
    setValues(
      values.filter(value => value.frontendId !== frontendId)
    )
  };


  const getRandomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
  };


  /**
   * Display Input Field
   *
   * @param {Number} frontendId
   * @param {String} value
   * @param {Function} setValue
   * @param {String} label
   * @param {Number} id - field ID in database
   * @return {*}
   */
  const displayField = (frontendId, value, setValue, label, id=null) => {
    // generate the key: existing fields should have a unique one,
    // so we just use the server ID and increase by 1000 (to divide them from new fields)
    const fieldKey = id ? 1000+id : frontendId;

    const props = {
      name: name+frontendId,
      label,
      value,
      setValue
    };
    switch (type) {
      // WYSIWYG
      case "editor":
        return <FormEditor {...props} key={fieldKey} />
      // <textarea/>
      case "textarea":
        return <FormTextarea {...props} key={fieldKey} />;
      // <input [text] />
      default:
        return <FormInput {...props} key={fieldKey} />
    }
  };


  /**
   * Convert Values to Input Fields
   */
  const fields = values.length ? values.map((valueArray, index) => {
    const {frontendId, value, id} = valueArray;

    // on change
    const handleChange = type !== "editor" ?
        e => {
          const {target} = e;
          updateValue(frontendId, target.value);
        }:
        (name, newValue)=> {
          updateValue(frontendId, newValue);
        };

    // on remove
    const handleRemove = e => {
      e.preventDefault();
      removeValue(frontendId);
    };

    // remove button
    const removeBtn = (
      <a href="#" className="color-red" onClick={handleRemove}>
        <i className="fa fa-times" aria-hidden="true"/>
      </a>
    );

    // label
    const fieldLabel = `${label} (вариант ${index+1})`;

    // display
    return (
      <div className="row" key={frontendId}>
        <div className="col-sm-12">
          {displayField(frontendId, value, handleChange, fieldLabel, id)}
          <button
            type="button" className="btn btn-link color-red" onClick={handleRemove}
            style={{top: 0, right: 0, position: "absolute"}}
          >
            <i className="fa fa-times" aria-hidden="true"></i>
          </button>
        </div>
      </div>
    )

  }) : null;


  /**
   * "Add New Field" button
   */
  const addButton = (
    <div className="text-right">
      <button type="button" onClick={addValue} className="btn btn-warning">
        + Добавить вариант
      </button>
    </div>
  );


  // display
  return (
    <div className="template-inputs mb-5">
      {fields}
      {addButton}
    </div>
  );


};


export default Form;