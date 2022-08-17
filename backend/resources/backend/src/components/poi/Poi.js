import React, {useState, useEffect} from 'react';
import ItemList from '../ItemList';
import ButtonAdd from "../tables/ButtonAdd";
import FormSelect from "../forms/FormSelect";
import {sendRequest} from "../../helpers/client-server";

const Poi = ({match}) => {

  // countries
  const [countries, setCountries] = useState([{
    name: "all",
    value: "Все страны"
  }]);
  const [chosenCountry, setChosenCountry] = useState("all");

  // cities
  const initialCities = [{
    name: "all",
    value: "Все города"
  }];
  const [cities, setCities] = useState(initialCities);
  const [chosenCity, setChosenCity] = useState("all");


  // load initial data
  useEffect(() => {
    loadCountries();
  }, []);

  // load city list when country change
  useEffect(() => {
    loadCities();
  }, [chosenCountry]);



  /**
   * Load Country list
   *
   * @return {Promise<void>}
   */
  const loadCountries = async () => {
    const response = await sendRequest("countries?order=name");
    if ( !response.data.items ) return;
    const countryList = response.data.items.map(country => {
      return {
        name: country.country_code,
        value: country.name
      }
    });
    setCountries([...countries, ...countryList]);
  };


  /**
   * Reset city list
   */
  const resetCityList = () => {
    setCities(initialCities);
    setChosenCity('all');
  };



  /**
   * Load City list
   *
   * @return {Promise<void>}
   */
  const loadCities = async () => {
    // empty <select> if country wasn't chosen
    if ( chosenCountry === 'all' ) {
      resetCityList();
      return;
    }

    // get list of cities
    const response = await sendRequest(`locations/all?country=${chosenCountry}`);

    // empty <select> if there's no cities
    if ( !response.data.items  ) {
      resetCityList();
      return;
    }

    // fill teh <select>
    const cityList = response.data.items.map(city => {
      return {
        name: city.api_id,
        value: city.name
      }
    });
    setCities([...initialCities, ...cityList]);
  };


  const handleCountryChange = e => {
    setChosenCountry(e.target.value);
  };


  const handleCityChange = e => {
    setChosenCity(e.target.value);
  };


  // display
  return (
    <div data-country={chosenCountry}>

      <div className="row mb-5">
        <div className="col-sm-4">
          <FormSelect
            name="country"
            label="Страна"
            value={chosenCountry}
            setValue={handleCountryChange}
            variants={countries}
          />
        </div>
        <div className="col-sm-4">
          <FormSelect
            name="city"
            label="Город"
            value={chosenCity}
            setValue={handleCityChange}
            variants={cities}
            disabled={chosenCountry === 'all'}
          />
        </div>
      </div>

      <ItemList
        tableData={["name", "country_code", "total_tours"]}
        columnWithLink={0}
        actions={["edit"]}
        type={`poi`}
        multilang={false}
        paginationEnabled={true}
        requestMethod='post'
        extraParams={{
          country: chosenCountry,
          city: chosenCity
        }}
        key={
          chosenCity === 'all' ?
            chosenCountry :
            chosenCity
        }
      />
    </div>
  )
};

export default Poi;

