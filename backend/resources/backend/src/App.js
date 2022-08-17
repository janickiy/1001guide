import React from 'react';
import { BrowserRouter, Route, Switch } from 'react-router-dom';

import {adminUrl} from './config';

import Sidebar from './components/Sidebar';
import Dummy from './components/Dummy';
import Languages from './components/languages/Languages';
import LanguageCreate from './components/languages/LanguageCreate';
import LanguageEdit from './components/languages/LanguageEdit';
import LocalSettings from './components/LocalSettings';
import Countries from "./components/countries/Countries";
import CountryCreate from "./components/countries/CountryCreate";
import CountryEdit from "./components/countries/CountryEdit";
import Locations from "./components/locations/Locations";
import LocationEdit from "./components/locations/LocationEdit";
import LocationCreate from "./components/locations/LocationCreate";
import Currencies from "./components/currencies/Currencies";
import CurrencyCreate from "./components/currencies/CurrencyCreate";
import CurrencyEdit from "./components/currencies/CurrencyEdit";
import Templates from "./components/templates/Templates";
import Poi from "./components/poi/Poi"
import Tags from "./components/tags/Tags";
import TagsCreate from "./components/tags/TagsCreate";
import TagsEdit from "./components/tags/TagsEdit";
import ContentGenerator from "./components/content_generator/ContentGenerator";
import Codes from "./components/codes/Codes";
import CodeCreate from "./components/codes/CodeCreate";
import CodeEdit from "./components/codes/CodeEdit";
import Export from "./components/export/Export";
import Import from "./components/import/Import";


function App() {

  return (
    <div className="App container-full">
      <div className="row">
        <BrowserRouter>

          <Sidebar/>

          <div className="col-sm-9">
            <Switch>

              <Route path="/" exact component={Dummy} />

              <Route path={`${adminUrl}languages/`} exact component={Languages} />
              <Route path={`${adminUrl}languages/create/`} exact component={LanguageCreate} />
              <Route path={`${adminUrl}languages/:id/edit/`} exact component={LanguageEdit} />

              <Route path={`${adminUrl}currencies/`} exact component={Currencies} />
              <Route path={`${adminUrl}currencies/create/`} exact component={CurrencyCreate} />
              <Route path={`${adminUrl}currencies/:id/edit/`} exact component={CurrencyEdit} />

              <Route path={`${adminUrl}locations/`} exact component={Countries} />
              <Route path={`${adminUrl}locations/create`} exact component={CountryCreate} />
              <Route path={`${adminUrl}locations/:id/edit/`} exact component={CountryEdit} />

              <Route path={`${adminUrl}locations/:id/`} exact component={Locations} />
              <Route path={`${adminUrl}locations/:country_id/create`} exact component={LocationCreate} />
              <Route path={`${adminUrl}locations/:country_id/:id/edit`} exact component={LocationEdit} />

              <Route path={`${adminUrl}poi/`} exact component={Poi} />
              <Route path={`${adminUrl}poi/:id/edit`} exact component={LocationEdit} />

              <Route path={`${adminUrl}tags/`} exact component={Tags} />
              <Route path={`${adminUrl}tags/create`} exact component={TagsCreate} />
              <Route path={`${adminUrl}tags/:id/edit/`} exact component={TagsEdit} />

              <Route path={`${adminUrl}templates/`} exact component={Templates} />

              <Route path={`${adminUrl}settings/`} exact component={LocalSettings} />

              <Route path={`${adminUrl}generate/`} exact component={ContentGenerator} />

              <Route path={`${adminUrl}codes/`} exact component={Codes} />
              <Route path={`${adminUrl}codes/create`} exact component={CodeCreate} />
              <Route path={`${adminUrl}codes/:id/edit/`} exact component={CodeEdit} />

              <Route path={`${adminUrl}export/`} exact component={Export} />
              <Route path={`${adminUrl}import/`} exact component={Import} />

            </Switch>
          </div>

        </BrowserRouter>
      </div>
    </div>
  );
}

export default App;