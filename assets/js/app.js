// ./src/js/app.js

import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import '../css/app.css';
import Home from './components/InputPlan';

ReactDOM.render(<Router><Home /></Router>, document.getElementById('plan_inputs'));
