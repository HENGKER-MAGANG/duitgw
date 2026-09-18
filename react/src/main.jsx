import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import TransactionForm from './components/TransactionForm';
import UserChart from './components/UserChart';
import AdminChart from './components/AdminChart';
import AdminStatsTable from './components/AdminStatsTable';

function mount(id, Component, propsFn) {
  const el = document.getElementById(id);
  if (!el) return;
  const root = createRoot(el);
  root.render(createElement(Component, propsFn(el.dataset)));
}

mount('react-transaction-form', TransactionForm, (ds) => ({
  action: ds.action,
  redirect: ds.redirect,
  mode: ds.mode || 'create',
  categories: ds.categories ? JSON.parse(ds.categories) : [],
  initial: ds.transaction ? JSON.parse(ds.transaction) : null,
}));

mount('react-user-chart', UserChart, (ds) => ({
  apiUrl: ds.api,
}));

mount('react-admin-chart', AdminChart, (ds) => ({
  apiUrl: ds.api,
}));

mount('react-admin-stats', AdminStatsTable, (ds) => ({
  apiUrl: ds.api,
  userUrl: ds.userUrl,
}));
