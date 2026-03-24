/**
 * Script to generate location data JSON files from country-state-city package
 * Run with: node scripts/generate-location-data.js
 */

import { Country, State, City } from 'country-state-city';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const outputDir = path.join(__dirname, '..', 'public', 'data');

// Ensure output directory exists
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

// Get all countries with phone codes
const countries = Country.getAllCountries().map(country => ({
    id: country.isoCode,
    name: country.name,
    iso2: country.isoCode,
    iso3: country.iso3 || '',
    phonecode: country.phonecode,
    currency: country.currency,
    flag: country.flag,
    latitude: country.latitude,
    longitude: country.longitude
}));

// Get all states grouped by country
const statesByCountry = {};
Country.getAllCountries().forEach(country => {
    const states = State.getStatesOfCountry(country.isoCode).map(state => ({
        id: state.isoCode,
        name: state.name,
        countryCode: state.countryCode,
        latitude: state.latitude,
        longitude: state.longitude
    }));
    if (states.length > 0) {
        statesByCountry[country.isoCode] = states;
    }
});

// Get all cities grouped by state and country
const citiesByState = {};
Country.getAllCountries().forEach(country => {
    State.getStatesOfCountry(country.isoCode).forEach(state => {
        const key = `${country.isoCode}-${state.isoCode}`;
        const cities = City.getCitiesOfState(country.isoCode, state.isoCode).map(city => ({
            id: city.name,
            name: city.name,
            stateCode: city.stateCode,
            countryCode: city.countryCode,
            latitude: city.latitude,
            longitude: city.longitude
        }));
        if (cities.length > 0) {
            citiesByState[key] = cities;
        }
    });
});

// Write countries file
fs.writeFileSync(
    path.join(outputDir, 'countries.json'),
    JSON.stringify(countries, null, 2)
);
console.log(`✓ Generated countries.json (${countries.length} countries)`);

// Write states file
fs.writeFileSync(
    path.join(outputDir, 'states.json'),
    JSON.stringify(statesByCountry, null, 2)
);
console.log(`✓ Generated states.json (${Object.keys(statesByCountry).length} country entries)`);

// Write cities file
fs.writeFileSync(
    path.join(outputDir, 'cities.json'),
    JSON.stringify(citiesByState, null, 2)
);
console.log(`✓ Generated cities.json (${Object.keys(citiesByState).length} state entries)`);

console.log('\n✓ All location data files generated successfully!');
