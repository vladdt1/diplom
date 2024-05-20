// В файле router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import AuthComponent from '@/components/AuthComponent.vue';
import HomeComponent from '@/components/HomeComponent.vue';
import ProfileComponent from '@/components/student/ProfileComponent.vue';
import SearchComponent from '@/components/search/SearchComponent.vue';
import MybooksComponent from '@/components/student/mybooks/MybooksComponent.vue';
import AddBookComponent from '@/components/librarian/AddBookComponent.vue';
import ReservationComponent from '@/components/librarian/ReservationComponent.vue';
import SearchStudentsComponent from '@/components/librarian/searchStudent/SearchStudentsComponent.vue';
import BookSearchComponent from '@/components/search/BookSearchComponent.vue';
import MyBookViewComponent from '@/components/student/mybooks/MyBookViewComponent.vue';
import StudentsComponent from '@/components/librarian/searchStudent/StudentsComponent.vue';
import AddStudentAndLibrarian from '@/components/administrator/AddStudentAndLibrarian.vue';
import StatisticsComponent from '@/components/librarian/Statistics/StatisticsComponent.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Auth',
      component: AuthComponent,
      meta: { hideHeaderNav: true },
    },
    {
      path: '/home',
      name: 'Home',
      component: HomeComponent,
      props: (route) => ({ userId: route.query.userId }),
    },
    {
      path: '/profile',
      name: 'Profile',
      component: ProfileComponent,
    },
    {
      path: '/mybooks',
      name: 'Mybooks',
      component: MybooksComponent
    },
    {
      path: '/search',
      name: 'Search',
      component: SearchComponent,
      props: (route) => ({ userId: route.query.userId, searchTerm: route.query.q }),
    },
    {
      path: '/students',
      name: 'Students',
      component: SearchStudentsComponent,
    },
    {
      path: '/reservation',
      name: 'Reservation',
      component: ReservationComponent
    },
    {
      path: '/add-book',
      name: 'AddBook',
      component: AddBookComponent
    },
    {
      path: '/search-book/:id',
      name: 'bookSearch',
      component: BookSearchComponent
    },
    {
      path: '/mybooks/:id',
      name: 'MyBookView',
      component: MyBookViewComponent,
    },
    {
      path: '/search-student/:id',
      name: 'studentSearch',
      component: StudentsComponent
    },
    {
      path: '/admin',
      name: 'Admin',
      component: AddStudentAndLibrarian
    },
    {
      path: '/statistics',
      name: 'Statistics',
      component: StatisticsComponent
    },
  ],
});

export default router;
