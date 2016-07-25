import time
import json
import requests


def submit_data(data):
    url = 'http://52.24.231.219/Server-receiving.php'
    r = requests.post(url, {"data": json.dumps(data)})
    job_id = r.json()['BMDS_Service_Number']
    print('submitted job id: %s' % job_id)
    return job_id


def execute_run(bsn):
    url = 'http://52.24.231.219/BMDS_execution.php'
    data = {'bsn': bsn}
    print('Run execution started')
    r = requests.get(url, data)
    # print(r.content)
    print('Run execution complete')


def check_results(bsn):
    url = 'http://52.24.231.219/Server-report.php'
    data = bsn
    r = requests.post(url, data)
    print(r.content)
    try:
        return r.json()
    except ValueError:
        return False


def main():
    data = {
        "options": {
            "bmds_version": "BMDS2601",
            "emf_YN": "True"
        },
        "runs": [
            {
                "id": 29,
                "model_app_name": "DichoHill",
                "dfile": "Dichotomous-Hill \nBMDS_Model_Run \nC:/USEPA/BMDS/BMDS2601/Dorman2008/1-2008-AcroleinInhalation.dax \nC:/USEPA/BMDS/BMDS2601/Dorman2008/1-2008-Acrolein_Inhalation-DichHill-10Pct-4d.out \n4 \n500 0.00000001 0.00000001 0 1 1 0 0 \n0.1 0 0.95 \n-9999 -9999 -9999 -9999 \n0 \n-9999 -9999 -9999 -9999 \nDose Incidence NEGATIVE_RESPONSE \n0 0 12 \n0.2 0 12 \n0.6 7 5 \n1.8 11 0 \n"
            },
        ]
    }

    bsn = submit_data(data)
    print '\#bsn = ', bsn
    execute_run(bsn)


    while True:
        time.sleep(1)
        res = check_results(bsn)
        if res:
            break
    print(res)


if __name__ == '__main__':          # if run by itself, not be imported.
    main()