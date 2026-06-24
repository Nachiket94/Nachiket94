<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gdmo_model extends CI_Model
{
    public function get_rates()
    {
        return $this->db->order_by('effective_date', 'DESC')->order_by('id', 'DESC')->get('gdmo_rates')->result();
    }

    public function get_rate($id)
    {
        return $id ? $this->db->get_where('gdmo_rates', array('id' => $id))->row() : NULL;
    }

    public function save_rate($data, $id = NULL)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($id) {
            $this->db->where('id', $id)->update('gdmo_rates', $data);
            return $id;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('gdmo_rates', $data);
        return $this->db->insert_id();
    }

    public function get_tds_rates()
    {
        $this->db->select('gdmo_tds_rates.*, gdmo_masters.name AS gdmo_name');
        $this->db->from('gdmo_tds_rates');
        $this->db->join('gdmo_masters', 'gdmo_masters.id = gdmo_tds_rates.gdmo_id', 'left');
        return $this->db->order_by('effective_date', 'DESC')->order_by('id', 'DESC')->get()->result();
    }

    public function get_tds($id)
    {
        return $id ? $this->db->get_where('gdmo_tds_rates', array('id' => $id))->row() : NULL;
    }

    public function save_tds($data, $id = NULL)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($id) {
            $this->db->where('id', $id)->update('gdmo_tds_rates', $data);
            return $id;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('gdmo_tds_rates', $data);
        return $this->db->insert_id();
    }

    public function get_gdmos($active_only = TRUE)
    {
        $this->db->select('gdmo_masters.*, gdmo_rates.effective_date, gdmo_rates.shift_rate, gdmo_rates.emergency_rate');
        $this->db->from('gdmo_masters');
        $this->db->join('gdmo_rates', 'gdmo_rates.id = gdmo_masters.rate_id', 'left');
        if ($active_only) {
            $this->db->where('gdmo_masters.is_active', 1);
        }
        return $this->db->order_by('gdmo_masters.name', 'ASC')->get()->result();
    }

    public function get_gdmo($id)
    {
        return $id ? $this->db->get_where('gdmo_masters', array('id' => $id))->row() : NULL;
    }

    public function save_gdmo($data, $id = NULL)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($id) {
            $this->db->where('id', $id)->update('gdmo_masters', $data);
            return $id;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('gdmo_masters', $data);
        return $this->db->insert_id();
    }

    public function get_attendance_map($month)
    {
        $rows = $this->db->get_where('gdmo_attendance', array('attendance_month' => $month))->result();
        $map = array();
        foreach ($rows as $row) {
            $map[$row->gdmo_id] = $row;
        }
        return $map;
    }

    public function save_attendance($month, $gdmo_id, $row)
    {
        $data = array(
            'attendance_month' => $month,
            'gdmo_id' => $gdmo_id,
            'shift_a_days' => isset($row['shift_a_days']) ? $row['shift_a_days'] : 0,
            'shift_b_days' => isset($row['shift_b_days']) ? $row['shift_b_days'] : 0,
            'shift_c_days' => isset($row['shift_c_days']) ? $row['shift_c_days'] : 0,
            'emergency_days' => isset($row['emergency_days']) ? $row['emergency_days'] : 0,
            'extra_hours' => isset($row['extra_hours']) ? $row['extra_hours'] : 0,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $existing = $this->db->get_where('gdmo_attendance', array('attendance_month' => $month, 'gdmo_id' => $gdmo_id))->row();
        if ($existing) {
            return $this->db->where('id', $existing->id)->update('gdmo_attendance', $data);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('gdmo_attendance', $data);
    }

    public function create_salary($month)
    {
        $attendance = $this->get_attendance_map($month);
        foreach ($this->get_gdmos() as $gdmo) {
            if (!isset($attendance[$gdmo->id])) {
                continue;
            }
            $att = $attendance[$gdmo->id];
            $rate = $this->get_effective_rate($gdmo->rate_id, $month);
            if (!$rate) {
                continue;
            }
            $tds_percent = $this->get_effective_tds_percent($gdmo->id, $month);
            $shift_a_hours = $att->shift_a_days * 6;
            $shift_b_hours = $att->shift_b_days * 6;
            $shift_c_hours = $att->shift_c_days * 12;
            $shift_total_hours = $shift_a_hours + $shift_b_hours + $shift_c_hours;
            $shift_amount = $shift_total_hours * $rate->shift_rate;
            $emergency_hours = $att->emergency_days * 12;
            $emergency_amount = $emergency_hours * $rate->emergency_rate;
            $extra_amount = $att->extra_hours * $rate->shift_rate;
            $gross_amount = $shift_amount + $emergency_amount + $extra_amount;
            $tds_amount = round($gross_amount * $tds_percent / 100, 2);
            $net_amount = $gross_amount - $tds_amount;

            $data = array(
                'salary_month' => $month,
                'gdmo_id' => $gdmo->id,
                'gdmo_name' => $gdmo->name,
                'rate_id' => $rate->id,
                'shift_rate' => $rate->shift_rate,
                'emergency_rate' => $rate->emergency_rate,
                'tds_percent' => $tds_percent,
                'shift_a_days' => $att->shift_a_days,
                'shift_b_days' => $att->shift_b_days,
                'shift_c_days' => $att->shift_c_days,
                'emergency_days' => $att->emergency_days,
                'shift_a_hours' => $shift_a_hours,
                'shift_b_hours' => $shift_b_hours,
                'shift_c_hours' => $shift_c_hours,
                'shift_total_hours' => $shift_total_hours,
                'shift_amount' => $shift_amount,
                'emergency_hours' => $emergency_hours,
                'emergency_amount' => $emergency_amount,
                'extra_hours' => $att->extra_hours,
                'extra_amount' => $extra_amount,
                'gross_amount' => $gross_amount,
                'tds_amount' => $tds_amount,
                'net_amount' => $net_amount,
                'updated_at' => date('Y-m-d H:i:s')
            );
            $existing = $this->db->get_where('gdmo_salaries', array('salary_month' => $month, 'gdmo_id' => $gdmo->id))->row();
            if ($existing) {
                $this->db->where('id', $existing->id)->update('gdmo_salaries', $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('gdmo_salaries', $data);
            }
        }
    }

    public function get_effective_rate($rate_id, $month)
    {
        $date = $month . '-01';
        $base = $this->get_rate($rate_id);
        if (!$base) {
            return NULL;
        }
        return $this->db->where('effective_date <=', $date)
            ->where('effective_date <=', $base->effective_date)
            ->order_by('effective_date', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('gdmo_rates')->row() ?: $base;
    }

    public function get_effective_tds_percent($gdmo_id, $month)
    {
        $date = $month . '-01';
        $row = $this->db->where('effective_date <=', $date)
            ->group_start()
                ->where('gdmo_id', $gdmo_id)
                ->or_where('gdmo_id IS NULL', NULL, FALSE)
            ->group_end()
            ->order_by('gdmo_id IS NULL', 'ASC', FALSE)
            ->order_by('effective_date', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('gdmo_tds_rates')->row();
        return $row ? $row->tds_percent : 0;
    }

    public function get_salaries($month)
    {
        return $this->db->order_by('gdmo_name', 'ASC')->get_where('gdmo_salaries', array('salary_month' => $month))->result();
    }

    public function get_salary($id)
    {
        return $id ? $this->db->get_where('gdmo_salaries', array('id' => $id))->row() : NULL;
    }

    public function get_tds_report($from, $to)
    {
        $this->db->select('gdmo_name, SUM(net_amount) AS salary_amount, SUM(tds_amount) AS tds_amount, SUM(gross_amount) AS total_amount');
        $this->db->from('gdmo_salaries');
        $this->db->where('salary_month >=', $from);
        $this->db->where('salary_month <=', $to);
        $this->db->group_by('gdmo_id, gdmo_name');
        return $this->db->order_by('gdmo_name', 'ASC')->get()->result();
    }

    public function is_salary_locked($month)
    {
        return (bool) $this->db->get_where('gdmo_salary_locks', array('salary_month' => $month))->row();
    }

    public function lock_salary($month)
    {
        if ($this->is_salary_locked($month)) {
            return TRUE;
        }
        return $this->db->insert('gdmo_salary_locks', array('salary_month' => $month, 'locked_at' => date('Y-m-d H:i:s')));
    }
}
