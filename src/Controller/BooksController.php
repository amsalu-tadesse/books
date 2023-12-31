<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;

/**
 * Books Controller
 *
 * @property \App\Model\Table\BooksTable $Books
 */
class BooksController extends AppController
{


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {


        // Get the sort and direction from the query parameters
        $sortField = $this->request->getQuery('sort');
        $sortDirection = $this->request->getQuery('direction');

        if (empty($sortField)) {
            $sortField = 'id';
        }
        if (empty($sortDirection)) {
            $sortDirection = 'desc'; // show the latest on top.
        }


        $this->paginate = [
            'limit' => 10, // Number of records per page
        ];

        $query = $this->Books->find()
            ->contain(['Authors']);
        if ($sortDirection == 'asc') {
            $query = $query->orderAsc('Books.' . $sortField);
        } else {
            $query = $query->orderDesc('Books.' . $sortField);
        }

        $books = $this->paginate($query);

        $this->set(compact('books'));
    }

    /**
     * View method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $book = $this->Books->get($id, contain: ['Authors']);
        $this->set(compact('book'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $book = $this->Books->newEmptyEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }
        $authors = $this->Books->Authors->find('list', limit: 200)->all();
        $this->set(compact('book', 'authors'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $book = $this->Books->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }
        $authors = $this->Books->Authors->find('list', limit: 200)->all();
        $this->set(compact('book', 'authors'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Book id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $book = $this->Books->get($id);
        if ($this->Books->delete($book)) {
            $this->Flash->success(__('The book has been deleted.'));
            $response = new Response();
            $response = $response->withType('application/json')
                ->withStringBody(json_encode(['success' => true]));

            return $response;
        } else {
            $this->Flash->error(__('The book could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
