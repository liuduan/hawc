#!/usr/bin/env python
# -*- coding: utf-8 -*-

from copy import copy

from assessment.models import DoseUnits
from study.models import Study
from utils.helper import FlatFileExporter

from . import models


def get_gen_species_strain_sex(e, withN=False):

    gen = e['animal_group']['generation']
    if len(gen) > 0:
        gen += " "

    ns_txt = ""
    if withN:
        ns = [eg["n"] for eg in e["groups"]]
        ns_txt = " " + models.EndpointGroup.getNRangeText(ns)

    return u"{}{}, {} ({}{})".format(
        gen,
        e['animal_group']['species'],
        e['animal_group']['strain'],
        e['animal_group']['sex_symbol'],
        ns_txt
    )


def get_treatment_period(exp, dr):
    txt = exp["type"].lower()
    if txt.find("(") >= 0:
        txt = txt[:txt.find("(")]

    if dr["duration_exposure_text"]:
        txt = u"{0} ({1})".format(txt, dr["duration_exposure_text"])

    return txt


class EndpointFlatComplete(FlatFileExporter):
    """
    Returns a complete export of all data required to rebuild the the
    animal bioassay study type from scratch.
    """

    def _get_header_row(self):
        self.doses = DoseUnits.get_animal_units(self.kwargs.get('assessment'))

        header = []
        header.extend(Study.flat_complete_header_row())
        header.extend(models.Experiment.flat_complete_header_row())
        header.extend(models.AnimalGroup.flat_complete_header_row())
        header.extend(models.DosingRegime.flat_complete_header_row())
        header.extend(models.Endpoint.flat_complete_header_row())
        header.extend([u'doses-{}'.format(d) for d in self.doses])
        header.extend(models.EndpointGroup.flat_complete_header_row())
        return header

    def _get_data_rows(self):
        rows = []
        for obj in self.queryset:
            ser = obj.get_json(json_encode=False)
            row = []
            row.extend(Study.flat_complete_data_row(ser['animal_group']['experiment']['study']))
            row.extend(models.Experiment.flat_complete_data_row(ser['animal_group']['experiment']))
            row.extend(models.AnimalGroup.flat_complete_data_row(ser['animal_group']))
            row.extend(models.DosingRegime.flat_complete_data_row(ser['animal_group']['dosing_regime']))
            row.extend(models.Endpoint.flat_complete_data_row(ser))
            for i, eg in enumerate(ser['groups']):
                row_copy = copy(row)
                row_copy.extend(models.DoseGroup.flat_complete_data_row(
                    ser['animal_group']['dosing_regime']['doses'],
                    self.doses, i))
                row_copy.extend(models.EndpointGroup.flat_complete_data_row(eg, ser))
                rows.append(row_copy)

        return rows


class EndpointFlatDataPivot(FlatFileExporter):
    """
    Return a subset of frequently-used data for generation of data-pivot
    visualizations.
    """

    def _get_header_row(self):
        return [
            'study id',
            'study name',
            'study published',

            'experiment id',
            'experiment name',
            'chemical',

            'animal group id',
            'animal group name',
            'lifestage exposed',
            'lifestage assessed',
            'species',
            'species strain',
            'generation',
            'animal description',
            'animal description (with N)',
            'sex',
            'route',
            'treatment period',
            'duration exposure',

            'endpoint id',
            'endpoint name',
            'system',
            'organ',
            'effect',
            'effect subtype',
            'diagnostic',
            'tags',
            'observation time',
            'data type',
            'doses',
            'dose units',
            'response units',

            'low_dose',
            'NOEL',
            'LOEL',
            'FEL',
            'high_dose',

            'BMD model name',
            'BMDL',
            'BMD',
            'BMDU',
            'CSF',

            'key',
            'dose index',
            'dose',
            'N',
            'incidence',
            'response',
            'variance',
            'percent control mean',
            'percent control low',
            'percent control high'
        ]

    def _get_data_rows(self):

        units = self.kwargs.get('dose', None)

        def get_doses_list(ser):
            # compact the dose-list to only one set of dose-units; using the
            # selected dose-units if specified, else others
            if units:
                units_id = units.id
            else:
                units_id = ser['animal_group']['dosing_regime']['doses'][0]['dose_units']['id']
            return [
                d for d in ser['animal_group']['dosing_regime']['doses']
                if units_id == d['dose_units']['id']
            ]

        def get_dose_units(doses):
            return doses[0]['dose_units']['name']

        def get_doses_str(doses):
            values = u', '.join([str(float(d['dose'])) for d in doses])
            return u"{0} {1}".format(values, get_dose_units(doses))

        def get_dose(doses, idx):
            for dose in doses:
                if dose['dose_group_id'] == idx:
                    return float(dose['dose'])
            return None

        def get_species_strain(e):
            return u"{} {}".format(
                e['animal_group']['species'],
                e['animal_group']['strain']
            )

        def get_tags(e):
            effs = [tag["name"] for tag in e["effects"]]
            if len(effs) > 0:
                return u"|{0}|".format(u"|".join(effs))
            return ""

        rows = []
        for obj in self.queryset:
            ser = obj.get_json(json_encode=False)
            doses = get_doses_list(ser)

            # build endpoint-group independent data
            row = [
                ser['animal_group']['experiment']['study']['id'],
                ser['animal_group']['experiment']['study']['short_citation'],
                ser['animal_group']['experiment']['study']['published'],

                ser['animal_group']['experiment']['id'],
                ser['animal_group']['experiment']['name'],
                ser['animal_group']['experiment']['chemical'],

                ser['animal_group']['id'],
                ser['animal_group']['name'],
                ser['animal_group']['lifestage_exposed'],
                ser['animal_group']['lifestage_assessed'],
                ser['animal_group']['species'],
                get_species_strain(ser),
                ser['animal_group']['generation'],
                get_gen_species_strain_sex(ser, withN=False),
                get_gen_species_strain_sex(ser, withN=True),
                ser['animal_group']['sex'],
                ser['animal_group']['dosing_regime']['route_of_exposure'].lower(),
                get_treatment_period(ser['animal_group']['experiment'],
                                     ser['animal_group']['dosing_regime']),
                ser['animal_group']['dosing_regime']['duration_exposure_text'],

                ser['id'],
                ser['name'],
                ser['system'],
                ser['organ'],
                ser['effect'],
                ser['effect_subtype'],
                ser['diagnostic'],
                get_tags(ser),
                ser['observation_time_text'],
                ser['data_type_label'],
                get_doses_str(doses),
                get_dose_units(doses),
                ser['response_units'],
            ]

            # dose-group specific information
            if len(ser['groups']) > 1:
                row.extend([
                    get_dose(doses, 1),  # first non-zero dose
                    get_dose(doses, ser['NOEL']),
                    get_dose(doses, ser['LOEL']),
                    get_dose(doses, ser['FEL']),
                    get_dose(doses, len(ser['groups'])-1),
                ])
            else:
                row.extend([None]*5)

            # BMD information
            if ser['BMD'] and units and ser['BMD'].get('outputs')\
                    and ser['BMD']['dose_units_id'] == units.id:
                row.extend([
                    ser['BMD']['outputs']['model_name'],
                    ser['BMD']['outputs']['BMDL'],
                    ser['BMD']['outputs']['BMD'],
                    ser['BMD']['outputs']['BMDU'],
                    ser['BMD']['outputs']['CSF']
                ])
            else:
                row.extend([None]*5)

            # endpoint-group information
            for i, eg in enumerate(ser['groups']):
                row_copy = copy(row)
                row_copy.extend([
                    eg['id'],
                    eg['dose_group_id'],
                    get_dose(doses, i),
                    eg['n'],
                    eg['incidence'],
                    eg['response'],
                    eg['stdev'],
                    eg['percentControlMean'],
                    eg['percentControlLow'],
                    eg['percentControlHigh']
                ])
                rows.append(row_copy)

        return rows


class EndpointSummary(FlatFileExporter):
    def _get_header_row(self):
        return [
            "study-short_citation",
            "study-study_identifier",
            "experiment-chemical",
            "animal_group-name",
            "animal_group-sex",
            "animal description (with n)",
            "dosing_regime-route_of_exposure",
            "dosing_regime-duration_exposure_text",
            "species-name",
            "strain-name",
            "endpoint-id",
            "endpoint-url",
            "endpoint-system",
            "endpoint-organ",
            "endpoint-effect",
            "endpoint-name",
            "endpoint-observation_time",
            "endpoint-response_units",
            "Dose units",
            "Doses",
            "Responses",
            "Doses and responses",
            "Response direction",
        ]

    def _get_data_rows(self):

        def getDoseUnits(doses):
            return set(sorted([
               d['dose_units']['name'] for d in doses
            ]))

        def getDoses(doses, unit):

            doses = [
                d['dose'] for d in doses
                if d['dose_units']['name'] == unit
            ]
            return ["{0:g}".format(d) for d in doses]

        def getResponses(groups):
            resps = []
            for grp in groups:
                txt = ""
                if grp['isReported']:
                    if grp["response"] is not None:
                        txt = "{0:g}".format(grp["response"])
                    else:
                        txt = "{0:g}".format(grp["incidence"])
                    if grp['variance'] is not None:
                        txt = u"{0} ± {1:g}".format(txt, grp['variance'])
                resps.append(txt)
            return resps

        def getDR(doses, responses, units):
            txts = []
            for i in xrange(len(doses)):
                if len(responses) > i and len(responses[i]) > 0:
                    txt = u"{} {}: {}".format(doses[i], units, responses[i])
                    txts.append(txt)
            return u", ".join(txts)

        def getResponseDirection(responses, data_type):
            txt = u"↔"
            for resp in responses:
                if resp['significant']:
                    if data_type in ["C", "P"]:
                        if resp["response"] > responses[0]["response"]:
                            txt = u"↑"
                        else:
                            txt = u"↓"
                    else:
                        txt = u"↑"
                    break
            return txt

        rows = []
        for obj in self.queryset:
            ser = obj.get_json(json_encode=False)

            doses = ser['animal_group']['dosing_regime']['doses']
            units = getDoseUnits(doses)

            # build endpoint-group independent data
            row = [
                ser['animal_group']['experiment']['study']['short_citation'],
                ser['animal_group']['experiment']['study']['study_identifier'],
                ser['animal_group']['experiment']['chemical'],
                ser['animal_group']['name'],
                ser['animal_group']['sex'],
                get_gen_species_strain_sex(ser, withN=True),
                ser['animal_group']['dosing_regime']['route_of_exposure'],
                get_treatment_period(ser['animal_group']['experiment'],
                                     ser['animal_group']['dosing_regime']),
                ser['animal_group']['species'],
                ser['animal_group']['strain'],
                ser['id'],
                ser['url'],
                ser['system'],
                ser['organ'],
                ser['effect'],
                ser['name'],
                ser['observation_time_text'],
                ser['response_units'],
            ]

            responsesList = getResponses(ser['groups'])
            responseDirection = getResponseDirection(ser['groups'], ser['data_type'])
            for unit in units:
                row_copy = copy(row)
                dosesList = getDoses(doses, unit)
                row_copy.extend([
                    unit,                   # "units"
                    u", ".join(dosesList),  # Doses
                    u", ".join(responsesList),  # Responses w/ units
                    getDR(dosesList, responsesList, unit),
                    responseDirection
                ])
                rows.append(row_copy)

        return rows
