<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gdmo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Gdmo_model', 'gdmo');
        $this->load->helper(array('url', 'form'));
        $this->load->library('session');
    }

    public function index()
    {
        $this->load_view('gdmo/dashboard', array('title' => 'GDMO'));
    }

    public function rate_master()
    {
        if ($this->input->post()) {
            $data = array(
                'effective_date' => $this->input->post('effective_date', TRUE),
                'shift_rate' => $this->input->post('shift_rate', TRUE),
                'emergency_rate' => $this->input->post('emergency_rate', TRUE),
                'remarks' => $this->input->post('remarks', TRUE)
            );
            $this->gdmo->save_rate($data, $this->input->post('id', TRUE));
            $this->session->set_flashdata('success', 'Rate saved successfully.');
            redirect('gdmo/rate_master');
        }

        $this->load_view('gdmo/rate_master', array(
            'title' => 'GDMO Rate Master',
            'rates' => $this->gdmo->get_rates(),
            'edit' => $this->gdmo->get_rate($this->input->get('edit', TRUE))
        ));
    }

    public function tds_master()
    {
        if ($this->input->post()) {
            $this->gdmo->save_tds(array(
                'gdmo_id' => $this->input->post('gdmo_id', TRUE) ?: NULL,
                'effective_date' => $this->input->post('effective_date', TRUE),
                'tds_percent' => $this->input->post('tds_percent', TRUE),
                'remarks' => $this->input->post('remarks', TRUE)
            ), $this->input->post('id', TRUE));
            $this->session->set_flashdata('success', 'TDS saved successfully.');
            redirect('gdmo/tds_master');
        }

        $this->load_view('gdmo/tds_master', array(
            'title' => 'GDMO TDS Master',
            'tds_rates' => $this->gdmo->get_tds_rates(),
            'gdmos' => $this->gdmo->get_gdmos(),
            'edit' => $this->gdmo->get_tds($this->input->get('edit', TRUE))
        ));
    }

    public function gdmo_master()
    {
        if ($this->input->post()) {
            $this->gdmo->save_gdmo(array(
                'name' => $this->input->post('name', TRUE),
                'rate_id' => $this->input->post('rate_id', TRUE),
                'is_active' => $this->input->post('is_active', TRUE) ? 1 : 0
            ), $this->input->post('id', TRUE));
            $this->session->set_flashdata('success', 'GDMO saved successfully.');
            redirect('gdmo/gdmo_master');
        }

        $this->load_view('gdmo/gdmo_master', array(
            'title' => 'GDMO Master',
            'gdmos' => $this->gdmo->get_gdmos(FALSE),
            'rates' => $this->gdmo->get_rates(),
            'edit' => $this->gdmo->get_gdmo($this->input->get('edit', TRUE))
        ));
    }

    public function attendance()
    {
        $month = $this->input->get('month', TRUE) ?: date('Y-m');
        if ($this->input->post()) {
            $month = $this->input->post('month', TRUE);
            if ($this->gdmo->is_salary_locked($month)) {
                $this->session->set_flashdata('error', 'Salary is locked for this month. Attendance cannot be modified.');
                redirect('gdmo/attendance?month=' . rawurlencode($month));
            }
            foreach ((array) $this->input->post('attendance', TRUE) as $gdmo_id => $row) {
                $this->gdmo->save_attendance($month, $gdmo_id, $row);
            }
            $this->session->set_flashdata('success', 'Attendance saved successfully.');
            redirect('gdmo/attendance?month=' . rawurlencode($month));
        }

        $this->load_view('gdmo/attendance', array(
            'title' => 'GDMO Attendance',
            'month' => $month,
            'gdmos' => $this->gdmo->get_gdmos(),
            'attendance' => $this->gdmo->get_attendance_map($month),
            'locked' => $this->gdmo->is_salary_locked($month)
        ));
    }

    public function create_salary()
    {
        $month = $this->input->post('month', TRUE) ?: ($this->input->get('month', TRUE) ?: date('Y-m'));
        if ($this->input->post('create')) {
            if ($this->gdmo->is_salary_locked($month)) {
                $this->session->set_flashdata('error', 'Salary is locked for this month.');
            } else {
                $this->gdmo->create_salary($month);
                $this->session->set_flashdata('success', 'Salary created successfully.');
            }
            redirect('gdmo/view_salary?month=' . rawurlencode($month));
        }

        $this->load_view('gdmo/create_salary', array('title' => 'Create GDMO Salary', 'month' => $month));
    }

    public function view_salary()
    {
        $month = $this->input->get('month', TRUE) ?: date('Y-m');
        $rows = $this->gdmo->get_salaries($month);
        if ($this->input->get('export', TRUE) === 'excel') {
            $this->salary_export($rows, 'gdmo_salary_' . $month . '.csv');
            return;
        }
        $this->load_view('gdmo/view_salary', array('title' => 'View GDMO Salary', 'month' => $month, 'rows' => $rows));
    }

    public function salary_slip($id = NULL)
    {
        $row = $this->gdmo->get_salary($id);
        if (!$row) {
            show_404();
        }
        $this->load_view('gdmo/salary_slip', array('title' => 'GDMO Salary Slip', 'row' => $row));
    }

    public function tds_report()
    {
        $from = $this->input->get('from_month', TRUE) ?: date('Y-m');
        $to = $this->input->get('to_month', TRUE) ?: $from;
        $rows = $this->gdmo->get_tds_report($from, $to);
        if ($this->input->get('export', TRUE) === 'excel') {
            $this->tds_export($rows, 'gdmo_tds_report_' . $from . '_to_' . $to . '.csv');
            return;
        }
        $this->load_view('gdmo/tds_report', array('title' => 'GDMO TDS Report', 'from' => $from, 'to' => $to, 'rows' => $rows));
    }

    public function lock_salary()
    {
        $month = $this->input->post('month', TRUE) ?: ($this->input->get('month', TRUE) ?: date('Y-m'));
        if ($this->input->post('lock')) {
            $this->gdmo->lock_salary($month);
            $this->session->set_flashdata('success', 'Salary locked successfully.');
            redirect('gdmo/lock_salary?month=' . rawurlencode($month));
        }
        $this->load_view('gdmo/lock_salary', array('title' => 'Lock GDMO Salary', 'month' => $month, 'locked' => $this->gdmo->is_salary_locked($month)));
    }

    private function load_view($view, $data)
    {
        if (file_exists(APPPATH . 'views/header.php')) {
            $this->load->view('header', $data);
            $this->load->view($view, $data);
            $this->load->view('footer');
        } else {
            $this->load->view($view, $data);
        }
    }

    private function salary_export($rows, $filename)
    {
        $this->csv_headers($filename);
        $out = fopen('php://output', 'w');
        fputcsv($out, array('Name', 'Month', 'Shift Rate', 'Shift A Hour', 'Shift B Hour', 'Shift C Hour', 'Shift Total', 'Shift Amount', 'Emergency Rate', 'Emergency Hour', 'Emergency Total', 'Extra Hour', 'Extra Amount', 'Total Amount', 'TDS', 'Net Amount'));
        foreach ($rows as $row) {
            fputcsv($out, array($row->gdmo_name, $row->salary_month, $row->shift_rate, $row->shift_a_hours, $row->shift_b_hours, $row->shift_c_hours, $row->shift_total_hours, $row->shift_amount, $row->emergency_rate, $row->emergency_hours, $row->emergency_amount, $row->extra_hours, $row->extra_amount, $row->gross_amount, $row->tds_amount, $row->net_amount));
        }
        fclose($out);
    }

    private function tds_export($rows, $filename)
    {
        $this->csv_headers($filename);
        $out = fopen('php://output', 'w');
        fputcsv($out, array('Sl.No.', 'GDMO Name', 'Salary Amount', 'TDS', 'Total'));
        $i = 1;
        foreach ($rows as $row) {
            fputcsv($out, array($i++, $row->gdmo_name, $row->salary_amount, $row->tds_amount, $row->total_amount));
        }
        fclose($out);
    }

    private function csv_headers($filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    }
}
