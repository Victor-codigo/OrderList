<?php

declare(strict_types=1);

namespace Common\Domain\Validation\Common;

enum VALIDATION_ERRORS
{
    case OK;

    case NOT_BLANK;
    case NOT_NULL;

    case EMAIL;

    case STRING_MIN;
    case STRING_MAX;
    case STRING_INVALID_CHARACTERS;
    case STRING_NOT_EQUAL_LENGTH;
    case STRING_TOO_LONG;
    case STRING_TOO_SHORT;

    case TYPE;

    case EQUAL_TO;
    case NOT_EQUAL_TO;

    case IDENTICAL_TO;
    case NOT_IDENTICAL_TO;

    case LESS_THAN;
    case LESS_THAN_OR_EQUAL;

    case GREATER_THAN;
    case GREATER_THAN_OR_EQUAL;

    case RANGE_TOO_LOW;
    case RANGE_TOO_HIGH;
    case RANGE_NOT_IN_RANGE;
    case RANGE_INVALID_CHARACTERS;

    case UNIQUE;

    case POSITIVE;
    case POSITIVE_OR_ZERO;

    case NEGATIVE;
    case NEGATIVE_OR_ZERO;

    case DATE_INVALID;
    case DATE_INVALID_FORMAT;

    case DATETIME_INVALID_DATE;
    case DATETIME_INVALID_FORMAT;
    case DATETIME_INVALID_TIME;

    case TIME_INVALID_FORMAT;
    case TIME_INVALID_TIME;

    case TIMEZONE_IDENTIFIER;
    case TIMEZONE_IDENTIFIER_IN_COUNTRY;
    case TIMEZONE_IDENTIFIER_IN_ZONE;
    case TIMEZONE_IDENTIFIER_IN_INTL;

    case FILE_INVALID_MIME_TYPE;
    case FILE_NOT_FOUND;
    case FILE_NOT_READABLE;
    case FILE_TOO_LARGE;
    case FILE_EMPTY;

    case FILE_IMAGE_RATIO_TOO_SMALL;
    case FILE_IMAGE_RATIO_TOO_BIG;
    case FILE_IMAGE_TOO_NARROW;
    case FILE_IMAGE_TOO_WIDE;
    case FILE_IMAGE_TOO_LOW;
    case FILE_IMAGE_TOO_HIGH;
    case FILE_IMAGE_TOO_LARGE;
    case FILE_IMAGE_TOO_FEW_PIXEL;
    case FILE_IMAGE_TOO_MANY_PIXEL;
    case FILE_IMAGE_LANDSCAPE_NOT_ALLOWED;
    case FILE_IMAGE_PORTRAIT_NOT_ALLOWED;
    case FILE_IMAGE_SQUARE_NOT_ALLOWED;
    case FILE_IMAGE_SIZE_NOT_DETECTED;
    case FILE_IMAGE_CORRUPTED_IMAGE;

    case FILE_UPLOAD_FORM_SIZE;
    case FILE_UPLOAD_INIT_SIZE;
    case FILE_UPLOAD_CANT_WRITE;
    case FILE_UPLOAD_EXTENSION;
    case FILE_UPLOAD_NO_FILE;
    case FILE_UPLOAD_NO_TMP_DIR;
    case FILE_UPLOAD_OK;
    case FILE_UPLOAD_PARTIAL;

    case CHOICE_NOT_SUCH;
    case CHOICE_TOO_FEW;
    case CHOICE_TOO_MUCH;

    case UUID_INVALID_CHARACTERS;
    case UUID_INVALID_HYPHEN_PLACEMENT;
    case UUID_INVALID_VARIANT;
    case UUID_INVALID_VERSION;
    case UUID_TOO_LONG;
    case UUID_TOO_SHORT;

    case REGEX_FAIL;

    case ALPHANUMERIC;
    case ALPHANUMERIC_WITH_WHITESPACE;

    case URL;

    case LANGUAGE;

    case JSON;

    case ITERABLE_NOT_EQUAL;
    case ITERABLE_TOO_FEW;
    case ITERABLE_TOO_MANY;
    case ITERABLE_DIVISIBLE_BY;
}
