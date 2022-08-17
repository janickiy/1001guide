import React, {useState, useEffect} from 'react';
import {sendRequest} from "../../helpers/client-server";


const ContentGenerator = () => {

  // page types
  const pageTypes = {
    country: "Страна",
    location: "Город",
    poi: "Достопримечательность",
    tag: "Тег"
  };
  const [pageTypesSelected, setPageTypesSelected] = useState(Object.keys(pageTypes));

  // languages
  const [languages, setLanguages] = useState({});
  const [languagesSelected, setLanguagesSelected] = useState([]);

  // status
  const [status, setStatus] = useState('checking');

  // loading
  const [generationRunning, setGenerationRunning] = useState(false);


  // first run
  useEffect(() => {

    // get list of languages
    sendRequest('languages/codes')
    .then(response => {
      setLanguages(
        // convert language list to {ru: "ru", en: "en"...} format
        response.data.items.reduce((acc, current) => {
          acc[current] = current;
          return acc;
        }, {})
      );
      // mark all languages as selected
      setLanguagesSelected(response.data.items);
    })
    .catch(error => {
      console.log(error.toString());
    });

    // get generation status
    setGenerationStatus();

    // set timer on checking status
    const timer = setInterval(setGenerationStatus, 10000);

    return ()=> {
      clearInterval(timer);
    };

  }, []);


  // DEV: track language changes
  useEffect(()=> {
    console.log(languages, languagesSelected);
  }, [languages, languagesSelected]);


  const setGenerationStatus = () => {
    sendRequest('templates/generate/status', 'post')
    .then(response => {
      setStatus(response.data.status);
    })
    .catch(error => {
      console.log(error.toString());
    });
  };


  /**
   * Convert Object to Checkbox List
   *
   * @param {Object} obj - {name: label, name2: label2...}
   * @param {Array} selectedItems
   * @param {Function} setSelectedItems
   * @return {*[]}
   */
  const objectToCheckBoxes = (obj, selectedItems, setSelectedItems) => {
    return Object.keys(obj).map((value, index) => {

      // is Checkbox marked as checked?
      const isChecked = selectedItems.includes(value);

      // handle check
      const handleChange = () => {
        setSelectedItems(
          isChecked ?
            // if uncheck - remove
            selectedItems.filter(selectedValue => selectedValue !== value):
            // if check - add
            [...selectedItems, value]
        )
      };

      return (
        <div className="form-check" key={index}>
          <input className="form-check-input" type="checkbox"
                 id={value} checked={isChecked} onChange={handleChange}
          />
          <label className="form-check-label" htmlFor={value}>
            {obj[value]}
          </label>
        </div>
      )
    });
  };


  /**
   * Run generation proccess
   *
   * @return {Promise<void>}
   */
  const run = async () => {
    setStatus("in_progress");
    const response = await sendRequest(
      "templates/generate",
      "post",
      {
        langs: languagesSelected,
        page_types: pageTypesSelected
      },
      true
    );
    console.log(response);
  };


  const runBtn = () => {
    switch (status) {
      case 'checking':
        return null;
      case 'in_progress':
        return (
          <button
            type="button" className="btn btn-primary btn-lg btn-block" disabled={true}
          >
            Идёт генерация...
          </button>
        )
      default:
        return (
          <button
            type="button" className="btn btn-primary btn-lg btn-block"
            onClick={handleRunClick}
          >
            Запустить
          </button>
        )
    }
  };


  /**
   * Handle click on "Run" button
   *
   * @param e
   */
  const handleRunClick = e => {
    run();
  };


  return (
    <>
      <div className="row mb-5">

        <div className="col-sm-6">
          <h4>Языки</h4>
          {objectToCheckBoxes(languages, languagesSelected, setLanguagesSelected)}
        </div>

        <div className="col-sm-6">
          <h4>Типы страниц</h4>
          {objectToCheckBoxes(pageTypes, pageTypesSelected, setPageTypesSelected)}
        </div>

      </div>

      {runBtn()}

    </>
  )

};


export default ContentGenerator;