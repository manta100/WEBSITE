import axios from 'axios';
import csrfHeader from './csrf';

window.csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
